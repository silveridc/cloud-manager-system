<?php
namespace app\admin\logic;

use app\admin\model\RefundRecordModel;
use app\common\enum\OrderStatus;
use app\common\model\ClientCreditModel;
use app\common\model\ClientModel;
use app\common\model\OrderModel;
use think\facade\Event;

/**
 * 退款逻辑
 * @desc 退款逻辑
 * @uses \app\admin\logic\RefundLogic
 */
class RefundLogic
{
    /**
     * 申请退款
     * @author zhaoyj
     * @version v1
     * @param int $orderId - 订单ID
     * @param array $params - 退款参数
     * @param int $adminId - 管理员ID
     * @return int|array
     */
    public function apply(int $orderId, array $params, int $adminId = 0): int|array
    {
        $OrderModel = new OrderModel();
        $RefundRecordModel = new RefundRecordModel();

        $order = $OrderModel->where('id', $orderId)->find();
        if (!$order) {
            return [
                'status' => 404,
                'messages' => lang('order_not_found'),
                'time' => time(),
            ];
        }

        if ($order['status'] !== OrderStatus::Paid->value) {
            return [
                'status' => 400,
                'messages' => lang('order_cannot_refund'),
                'time' => time(),
            ];
        }

        $refundAmount = (float)($params['amount'] ?? $order['amount']);
        if ($refundAmount > (float)$order['amount']) {
            return [
                'status' => 400,
                'messages' => lang('refund_amount_exceed'),
                'time' => time(),
            ];
        }

        $RefundRecordModel->startTrans();
        try {
            $refundId = (int)$RefundRecordModel->insertGetId([
                'order_id' => $orderId,
                'client_id' => $order['client_id'],
                'admin_id' => $adminId,
                'amount' => $refundAmount,
                'reason' => $params['reason'] ?? '',
                'status' => 'pending',
                'create_time' => time(),
            ]);

            Event::trigger('after_refund_apply', [
                'refund_id' => $refundId,
                'order_id' => $orderId,
                'amount' => $refundAmount,
            ]);

            $RefundRecordModel->commit();
            return $refundId;
        } catch (\Throwable $e) {
            $RefundRecordModel->rollback();
            throw $e;
        }
    }

    /**
     * 审核退款
     * @author zhaoyj
     * @version v1
     * @param int $refundId - 退款记录ID
     * @param int $adminId - 管理员ID
     * @return array|void
     */
    public function approve(int $refundId, int $adminId)
    {
        $RefundRecordModel = new RefundRecordModel();
        $ClientModel = new ClientModel();
        $ClientCreditModel = new ClientCreditModel();
        $OrderModel = new OrderModel();

        $refund = $RefundRecordModel->where('id', $refundId)->find();
        if (!$refund) {
            return [
                'status' => 404,
                'messages' => lang('refund_not_found'),
                'time' => time(),
            ];
        }

        if ($refund['status'] !== 'pending') {
            return [
                'status' => 400,
                'messages' => lang('refund_already_processed'),
                'time' => time(),
            ];
        }

        $RefundRecordModel->startTrans();
        try {
            // 更新退款状态
            $RefundRecordModel->where('id', $refundId)->update([
                'status' => 'approved',
                'admin_id' => $adminId,
                'update_time' => time(),
            ]);

            // 退回余额（排他锁防止并发读取脏数据）
            $ClientModel->where('id', $refund['client_id'])
                ->lock(true)
                ->inc('credit', (float)$refund['amount'])
                ->update();

            // 读取inc后的最新余额
            $newBalance = (float)$ClientModel->where('id', $refund['client_id'])->value('credit');

            // 记录余额变动
            $ClientCreditModel->insert([
                'client_id' => $refund['client_id'],
                'type' => 'refund',
                'amount' => $refund['amount'],
                'balance' => $newBalance,
                'notes' => lang('refund_from_order', ['id' => $refund['order_id']]),
                'create_time' => time(),
            ]);

            // 更新订单状态
            $OrderModel->where('id', $refund['order_id'])->update([
                'status' => OrderStatus::Refunded->value,
                'update_time' => time(),
            ]);

            Event::trigger('after_refund_approved', [
                'refund_id' => $refundId,
                'order_id' => $refund['order_id'],
                'client_id' => $refund['client_id'],
            ]);

            $RefundRecordModel->commit();
        } catch (\Throwable $e) {
            $RefundRecordModel->rollback();
            throw $e;
        }
    }

    /**
     * 拒绝退款
     * @author zhaoyj
     * @version v1
     * @param int $refundId - 退款记录ID
     * @param string $reason - 拒绝原因
     * @param int $adminId - 管理员ID
     * @return array|void
     */
    public function reject(int $refundId, string $reason, int $adminId)
    {
        $RefundRecordModel = new RefundRecordModel();

        $refund = $RefundRecordModel->where('id', $refundId)->find();
        if (!$refund) {
            return [
                'status' => 404,
                'messages' => lang('refund_not_found'),
                'time' => time(),
            ];
        }

        if ($refund['status'] !== 'pending') {
            return [
                'status' => 400,
                'messages' => lang('refund_already_processed'),
                'time' => time(),
            ];
        }

        $RefundRecordModel->where('id', $refundId)->update([
            'status' => 'rejected',
            'admin_id' => $adminId,
            'reject_reason' => $reason,
            'update_time' => time(),
        ]);
    }

    /**
     * 获取退款记录列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function getList(array $params): array
    {
        $RefundRecordModel = new RefundRecordModel();

        $query = $RefundRecordModel->alias('r')
            ->leftJoin('client c', 'c.id = r.client_id')
            ->leftJoin('order o', 'o.id = r.order_id')
            ->field([
                'r.*',
                'c.username as client_name',
                'o.amount as order_amount',
            ]);

        if (!empty($params['status'])) {
            $query->where('r.status', $params['status']);
        }
        if (!empty($params['client_id'])) {
            $query->where('r.client_id', (int)$params['client_id']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('r.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }
}