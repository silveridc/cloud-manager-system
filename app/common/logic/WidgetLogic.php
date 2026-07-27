<?php
namespace app\common\logic;

use app\common\model\AdminWidgetModel;
use app\common\model\ClientModel;
use app\common\model\HostModel;
use app\common\model\OrderModel;

/**
 * 小组件逻辑
 * @desc 小组件逻辑
 * @uses \app\common\logic\WidgetLogic
 */
class WidgetLogic
{
    /**
     * 获取管理员小部件列表
     * @author zhaoyj
     * @version v1
     * @param int $adminId - 管理员id
     * @return array - 小部件列表
     */
    public function getAdminWidgets(int $adminId): array
    {
        $AdminWidgetModel = new AdminWidgetModel();
        return $AdminWidgetModel
            ->where('admin_id', $adminId)
            ->order('order', 'asc')
            ->select()->toArray();
    }

    /**
     * 获取客户仪表盘数据
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户id
     * @return array
     * @return array host - 主机统计(total/active/suspended/pending)
     * @return int unpaid_orders - 未支付订单数
     * @return string credit - 余额
     */
    public function getClientDashboard(int $clientId): array
    {
        // 汇总客户数据
        $HostModel = new HostModel();
        $hostStats = $HostModel
            ->where('client_id', $clientId)
            ->where('status', '<>', 'Deleted')
            ->field([
                'COUNT(*) as total',
                "SUM(CASE WHEN status='Active' THEN 1 ELSE 0 END) as active",
                "SUM(CASE WHEN status='Suspended' THEN 1 ELSE 0 END) as suspended",
                "SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending",
            ])
            ->find();

        $OrderModel = new OrderModel();
        $unpaidOrders = $OrderModel
            ->where('client_id', $clientId)
            ->where('status', 'Unpaid')
            ->count();

        $ClientModel = new ClientModel();
        $credit = $ClientModel
            ->where('id', $clientId)
            ->value('credit');

        return [
            'host' => $hostStats,
            'unpaid_orders' => $unpaidOrders,
            'credit' => amount_format($credit),
        ];
    }

    /**
     * 更新小部件排序
     * @author zhaoyj
     * @version v1
     * @param int $adminId - 管理员id
     * @param array $widgets - 小部件列表
     * @return void
     * @throws \Throwable
     */
    public function updateOrder(int $adminId, array $widgets): void
    {
        $AdminWidgetModel = new AdminWidgetModel();
        $AdminWidgetModel->startTrans();
        try {
            $AdminWidgetModel->where('admin_id', $adminId)->delete();
            $data = [];
            foreach ($widgets as $i => $widget) {
                $data[] = [
                    'admin_id' => $adminId,
                    'widget' => $widget['widget'] ?? $widget,
                    'order' => $i,
                ];
            }
            if ($data) {
                $AdminWidgetModel2 = new AdminWidgetModel();
                $AdminWidgetModel2->insertAll($data);
            }
            $AdminWidgetModel->commit();
        } catch (\Throwable $e) {
            $AdminWidgetModel->rollback();
            throw $e;
        }
    }
}