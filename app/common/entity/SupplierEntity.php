<?php
namespace app\common\entity;

use think\Entity;
use app\admin\model\SupplierModel;
/**
 * 供应商实体
 * @desc 供应商实体
 * @uses \app\common\entity\SupplierEntity
 */
class SupplierEntity extends Entity
{
    protected function getOptions(): array
    {
        return [
            'modelClass' => SupplierModel::class,
        ];
    }
    /**
     * @title 管理端供应商列表
     * @desc 管理端供应商列表
     * @author zhaoyj
     * @version v1
     */
    public function listForAdmin(array $params): array
    {
        $query = $this->model()->db();

        if (!empty($params['keywords'])) {
            $query->where('name|url', 'like', '%' . $params['keywords'] . '%');
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        // 批量加载上游产品数量
        if ($list) {
            $supplierIds = array_column($list, 'id');
            $productCounts = $this->model()->db()->name('upstream_product')
                ->whereIn('supplier_id', $supplierIds)
                ->field('supplier_id, COUNT(*) as total')
                ->group('supplier_id')
                ->column('total', 'supplier_id');

            foreach ($list as &$item) {
                $item['product_num'] = $productCounts[$item['id']] ?? 0;
            }
        }

        return ['list' => $list, 'count' => $count];
    }
}