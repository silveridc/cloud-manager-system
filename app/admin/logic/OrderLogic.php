<?php
namespace app\admin\logic;

use app\common\entity\OrderEntity;
use app\common\model\OrderModel;
use app\common\model\TransactionModel;
use app\common\enum\OrderStatus;
use think\facade\Event;

/**
 * 订单业务逻辑
 * @desc 订单业务逻辑
 * @uses \app\admin\logic\OrderLogic
 */
class OrderLogic
{
    protected OrderEntity $entity;
    protected OrderModel $model;

    public function __construct(OrderEntity $entity, OrderModel $model)
    {
        $this->entity = $entity;
        $this->model = $model;
    }

    /**
     * 获取订单列表（管理端）
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数（分页、筛选条件等）
     * @return array
     * @return array list - 订单列表
     * @return int count - 订单总数
     */
    public function GetOrderList(array $params): array
    {
        return $this->entity->listForAdmin($params);
    }

    /**
     * 获取客户订单列表（客户端）
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params - 查询参数（分页、筛选条件等）
     * @return array
     * @return array list - 订单列表
     * @return int count - 订单总数
     */
    public function getClientList(int $clientId, array $params): array
    {
        return $this->entity->listForClient($clientId, $params);
    }

    /**
     * 获取订单详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 订单ID
     * @param int|null $clientId - 客户ID（传入时验证归属关系，null表示管理端不限制）
     * @return array|null
     * @return int id - 订单ID
     * @return int client_id - 客户ID
     * @return string status - 订单状态
     * @return float amount - 订单金额
     * @return string gateway - 支付网关
     * @return int pay_time - 支付时间
     */
    public function GetOrderInfo(int $id, ?int $clientId = null): ?array
    {
        return $this->entity->detail($id, $clientId);
    }

    /**
     * 取消订单（仅未支付订单可取消）
     * @author zhaoyj
     * @version v1
     * @param int $id - 订单ID
     * @param int|null $clientId - 客户ID（传入时验证归属关系，null表示管理端不限制）
     * @return void
     * @throws \think\exception\ValidateException 订单不存在、不属于该客户或非未支付状态时抛出
     * @throws \Throwable 数据库事务异常时抛出
     */
    public function CancelOrder(int $id, ?int $clientId = null)
    {
        $order = $this->model->find($id);
        if (!$order) {
            return [
                'status' => 404,
                'messages' => lang('order_not_found'),
                'time' => time(),
            ];
        }

        if ($clientId !== null && $order->getAttr('client_id') != $clientId) {
            return [
                'status' => 404,
                'messages' => lang('order_not_found'),
                'time' => time(),
            ];
        }

        if ($order->getAttr('status') !== OrderStatus::Unpaid->value) {
            return [
                'status' => 400,
                'messages' => lang('order_cannot_cancel'),
                'time' => time(),
            ];
        }

        $order->startTrans();
        try {
            $order->save([
                'status' => OrderStatus::Cancelled->value,
            ]);

            Event::trigger('after_order_cancel', ['order_id' => $id]);

            $order->commit();
        } catch (\Throwable $e) {
            $order->rollback();
            throw $e;
        }
    }

    /**
     * 订单支付完成处理（使用排他锁防止并发双重支付）
     * @author zhaoyj
     * @version v1
     * @param int $orderId - 订单ID
     * @param string $transactionId - 第三方交易流水号（可选）
     * @param string $gateway - 支付网关标识（可选）
     * @return void
     * @throws \Throwable 数据库事务异常时抛出
     */
    public function payComplete(int $orderId, string $transactionId = '', string $gateway = ''): void
    {
        $this->model->startTrans();
        try {
            // 事务内加排他锁查询，防止并发双重支付
            $order = $this->model->lock(true)->find($orderId);
            if (!$order || $order->getAttr('status') !== OrderStatus::Unpaid->value) {
                $this->model->commit();
                return;
            }

            $order->save([
                'status' => OrderStatus::Paid->value,
                'pay_time' => time(),
                'gateway' => $gateway,
            ]);

            if ($transactionId) {
                $transaction = new TransactionModel();
                $transaction->save([
                    'client_id' => $order->getAttr('client_id'),
                    'order_id' => $orderId,
                    'amount' => $order->getAttr('amount'),
                    'gateway' => $gateway,
                    'transaction_id' => $transactionId,
                ]);
            }

            Event::trigger('after_order_paid', [
                'order_id' => $orderId,
                'client_id' => $order->getAttr('client_id'),
            ]);

            $this->model->commit();
        } catch (\Throwable $e) {
            $this->model->rollback();
            throw $e;
        }
    }
}
