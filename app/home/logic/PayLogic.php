<?php

namespace app\home\logic;

use app\common\enum\OrderStatus;
use think\facade\Event;
use app\common\model\ClientCreditModel;
use app\common\model\ClientModel;
use app\common\model\OrderModel;
use app\common\model\PluginModel;
use app\common\model\TransactionModel;

/**
 * 支付逻辑
 * @desc 支付逻辑
 * @uses \app\home\logic\PayLogic
 */
class PayLogic
{
    protected OrderModel $orderModel;
    protected ClientModel $clientModel;
    protected PluginModel $pluginModel;

    public function __construct(OrderModel $orderModel, ClientModel $clientModel, PluginModel $pluginModel)
    {
        $this->orderModel = $orderModel;
        $this->clientModel = $clientModel;
        $this->pluginModel = $pluginModel;
    }

    /**
     * 获取可用支付网关列表
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function getGatewayList(): array
    {
        return $this->pluginModel
            ->where('type', 'gateway')
            ->where('status', 1)
            ->field('id, name, title')
            ->select()->toArray();
    }

    /**
     * 发起支付
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param int $orderId - 订单ID
     * @param string $gateway - 支付网关标识
     * @return array
     * @return array order_id - 订单ID
     * @return array amount - 支付金额
     * @return array gateway - 支付网关
     */
    public function pay(int $clientId, int $orderId, string $gateway): array
    {
        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('client_id', $clientId)
            ->find();

        if (!$order) {
            return [
                'status' => 404,
                'messages' => lang('order_not_found'),
                'time' => time(),
            ];
        }

        if ($order->getAttr('status') !== OrderStatus::Unpaid->value) {
            return [
                'status' => 400,
                'messages' => lang('order_already_paid'),
                'time' => time(),
            ];
        }

        if ((float)$order->getAttr('amount') < 0) {
            return [
                'status' => 400,
                'messages' => lang('invalid_order_amount'),
                'time' => time(),
            ];
        }

        $gatewayPlugin = $this->pluginModel
            ->where('name', $gateway)
            ->where('type', 'gateway')
            ->where('status', 1)
            ->find();

        if (!$gatewayPlugin) {
            return [
                'status' => 404,
                'messages' => lang('gateway_not_found'),
                'time' => time(),
            ];
        }

        $order->save(['gateway' => $gateway]);

        Event::trigger('before_order_pay', [
            'order_id' => $orderId,
            'client_id' => $clientId,
            'gateway' => $gateway,
        ]);

        return [
            'order_id' => $orderId,
            'amount' => $order->getAttr('amount'),
            'gateway' => $gateway,
        ];
    }

    /**
     * 余额支付
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param int $orderId - 订单ID
     * @return array|void
     */
    public function creditPay(int $clientId, int $orderId): ?array
    {
        $this->orderModel->startTrans();
        try {
            $order = $this->orderModel
                ->where('id', $orderId)
                ->where('client_id', $clientId)
                ->lock(true)
                ->find();

            if (!$order) {
                $this->orderModel->rollback();
                return [
                    'status' => 404,
                    'messages' => lang('order_not_found'),
                    'time' => time(),
                ];
            }

            if ($order->getAttr('status') !== OrderStatus::Unpaid->value) {
                $this->orderModel->rollback();
                return [
                    'status' => 400,
                    'messages' => lang('order_already_paid'),
                    'time' => time(),
                ];
            }

            $amount = (float)$order->getAttr('amount');
            if ($amount < 0) {
                $this->orderModel->rollback();
                return [
                    'status' => 400,
                    'messages' => lang('invalid_order_amount'),
                    'time' => time(),
                ];
            }

            // 原子扣减余额，WHERE 条件防止并发超扣
            $ClientModel = new ClientModel();
            $affected = $ClientModel
                ->where('id', $clientId)
                ->where('credit', '>=', $amount)
                ->dec('credit', $amount)
                ->update();

            if ($affected <= 0) {
                $this->orderModel->rollback();
                return [
                    'status' => 400,
                    'messages' => lang('insufficient_credit'),
                    'time' => time(),
                ];
            }

            // 读取扣减后的最新余额
            $newCredit = (float)(new ClientModel())->where('id', $clientId)->value('credit');

            $creditRecord = new ClientCreditModel();
            $creditRecord->save([
                'client_id' => $clientId,
                'type' => 'pay',
                'amount' => -$amount,
                'balance' => $newCredit,
                'notes' => lang('credit_pay_order', ['id' => $orderId]),
            ]);

            $order->save([
                'status' => OrderStatus::Paid->value,
                'pay_time' => time(),
                'gateway' => 'credit',
                'credit_amount' => $amount,
            ]);

            $transaction = new TransactionModel();
            $transaction->save([
                'client_id' => $clientId,
                'order_id' => $orderId,
                'amount' => $amount,
                'gateway' => 'credit',
                'transaction_id' => 'CREDIT_' . $orderId . '_' . time(),
            ]);

            Event::trigger('after_order_paid', [
                'order_id' => $orderId,
                'client_id' => $clientId,
            ]);

            $this->orderModel->commit();
            return null;
        } catch (\Throwable $e) {
            $this->orderModel->rollback();
            throw $e;
        }
    }
}