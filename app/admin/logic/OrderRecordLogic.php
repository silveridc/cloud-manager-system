<?php
namespace app\admin\logic;

use app\admin\model\OrderRecordModel;

/**
 * 订单记录逻辑
 * @desc 订单记录逻辑
 * @uses \app\admin\logic\OrderRecordLogic
 */
class OrderRecordLogic
{
    /**
     * 获取订单记录列表
     * @author zhaoyj
     * @version v1
     * @param int $orderId - 订单ID
     * @param array $params - 查询参数
     * @return array
     */
    public function GetOrderRecordList(int $orderId, array $params): array
    {
        $OrderRecordModel = new OrderRecordModel();

        $query = $OrderRecordModel
            ->where('order_id', $orderId);

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建订单记录
     * @author zhaoyj
     * @version v1
     * @param int $orderId - 订单ID
     * @param array $params - 记录参数
     * @param int $adminId - 管理员ID
     * @return int
     */
    public function CreateOrderRecord(int $orderId, array $params, int $adminId = 0): int
    {
        $OrderRecordModel = new OrderRecordModel();

        return (int)$OrderRecordModel->insertGetId([
            'order_id' => $orderId,
            'admin_id' => $adminId,
            'content' => $params['content'] ?? '',
            'create_time' => time(),
        ]);
    }
}