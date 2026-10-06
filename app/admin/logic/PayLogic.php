<?php
namespace app\admin\logic;

use app\common\enum\OrderStatus;
use app\common\model\ClientCreditModel;
use app\common\model\ClientModel;
use app\common\model\OrderModel;
use app\common\model\PluginModel;
use app\common\model\TransactionModel;
use think\facade\Event;

/**
 * 支付逻辑
 * @desc 支付逻辑
 * @uses \app\admin\logic\PayLogic
 */
class PayLogic
{
    /**
     * 获取可用支付网关列表
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function getGatewayList(): array
    {
        $PluginModel = new PluginModel();

        return $PluginModel
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
     */
    public function pay(int $clientId, int $orderId, string $gateway): array
    {
        $OrderModel = new OrderModel();
        $PluginModel = new PluginModel();

        $order = $OrderModel
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

        if ($order['status'] !== OrderStatus::Unpaid->value) {
            return [
                'status' => 400,
                'messages' => lang('order_already_paid'),
                'time' => time(),
            ];
        }

        if ((float)$order['amount'] < 0) {
            return [
                'status' => 400,
                'messages' => lang('invalid_order_amount'),
                'time' => time(),
            ];
        }

        // 验证网关
        $gatewayPlugin = $PluginModel
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

        // 更新订单网关
        $OrderModel->where('id', $orderId)->update([
            'gateway' => $gateway,
            'update_time' => time(),
        ]);

        Event::trigger('before_order_pay', [
            'order_id' => $orderId,
            'client_id' => $clientId,
            'gateway' => $gateway,
        ]);

        // 调用网关插件获取支付参数（由 PluginManager 代理）
        return [
            'order_id' => $orderId,
            'amount' => $order['amount'],
            'gateway' => $gateway,
        ];
    }

    /**
     * 余额支付
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param int $orderId - 订单ID
     * @return array
     */
    public function creditPay(int $clientId, int $orderId)
    {
        $OrderModel = new OrderModel();
        $ClientModel = new ClientModel();
        $ClientCreditModel = new ClientCreditModel();
        $TransactionModel = new TransactionModel();

        $OrderModel->startTrans();
        try {
            $order = $OrderModel
                ->where('id', $orderId)
                ->where('client_id', $clientId)
                ->lock(true)
                ->find();

            if (!$order) {
                $OrderModel->rollback();
                return [
                    'status' => 404,
                    'messages' => lang('order_not_found'),
                    'time' => time(),
                ];
            }

            if ($order['status'] !== OrderStatus::Unpaid->value) {
                $OrderModel->rollback();
                return [
                    'status' => 400,
                    'messages' => lang('order_already_paid'),
                    'time' => time(),
                ];
            }

            $amount = (float)$order['amount'];
            if ($amount < 0) {
                $OrderModel->rollback();
                return [
                    'status' => 400,
                    'messages' => lang('invalid_order_amount'),
                    'time' => time(),
                ];
            }

            // 原子扣减余额，WHERE 条件防止并发超扣
            $affected = $ClientModel
                ->where('id', $clientId)
                ->where('credit', '>=', $amount)
                ->dec('credit', $amount)
                ->update();

            if ($affected <= 0) {
                $OrderModel->rollback();
                return [
                    'status' => 400,
                    'messages' => lang('insufficient_credit'),
                    'time' => time(),
                ];
            }

            // 读取扣减后的最新余额
            $newCredit = (float)$ClientModel->where('id', $clientId)->value('credit');

            // 记录余额变动
            $ClientCreditModel->insert([
                'client_id' => $clientId,
                'type' => 'pay',
                'amount' => -$amount,
                'balance' => $newCredit,
                'notes' => lang('credit_pay_order', ['id' => $orderId]),
                'create_time' => time(),
            ]);

            // 标记订单已支付
            $order->save([
                'status' => OrderStatus::Paid->value,
                'pay_time' => time(),
                'gateway' => 'credit',
                'credit_amount' => $amount,
            ]);

            // 记录交易
            $TransactionModel->insert([
                'client_id' => $clientId,
                'order_id' => $orderId,
                'amount' => $amount,
                'gateway' => 'credit',
                'transaction_id' => 'CREDIT_' . $orderId . '_' . time(),
                'create_time' => time(),
            ]);

            Event::trigger('after_order_paid', [
                'order_id' => $orderId,
                'client_id' => $clientId,
            ]);

            $OrderModel->commit();
        } catch (\Throwable $e) {
            $OrderModel->rollback();
            throw $e;
        }
    }
}