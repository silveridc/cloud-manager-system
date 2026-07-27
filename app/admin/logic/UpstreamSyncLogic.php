<?php
namespace app\admin\logic;

use app\admin\model\SupplierModel;
use app\common\model\UpstreamHostModel;
use think\facade\Event;

/**
 * 上游同步逻辑
 * @desc 上游同步逻辑
 * @uses \app\admin\logic\UpstreamSyncLogic
 */
class UpstreamSyncLogic
{
    /**
     * 同步上游产品
     * @author zhaoyj
     * @version v1
     * @param int $supplierId - 供应商ID
     * @return array
     */
    public function syncProducts(int $supplierId): array
    {
        $SupplierModel = new SupplierModel();

        $supplier = $SupplierModel->where('id', $supplierId)->find();
        if (!$supplier) {
            return [
                'status' => 404,
                'messages' => lang('supplier_not_found'),
                'time' => time(),
            ];
        }

        Event::trigger('before_upstream_sync', ['supplier_id' => $supplierId]);

        // TODO: 调用上游 API 获取产品列表并同步到本地
        // 这里只提供框架，实际实现需根据上游 API 文档

        Event::trigger('after_upstream_sync', ['supplier_id' => $supplierId]);

        return ['synced' => 0];
    }

    /**
     * 同步上游产品实例状态
     * @author zhaoyj
     * @version v1
     * @param int $hostId - 主机ID
     * @return array
     */
    public function syncHostStatus(int $hostId): array
    {
        $UpstreamHostModel = new UpstreamHostModel();

        $upstreamHost = $UpstreamHostModel->where('host_id', $hostId)->find();
        if (!$upstreamHost) {
            return ['status' => 400, 'messages' => 'no upstream binding'];
        }

        // TODO: 调用上游 API 获取实例状态

        return ['status' => 200, 'messages' => 'success'];
    }
}