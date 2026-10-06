<?php
namespace app\common\entity;

use think\Entity;
use app\common\model\ProductModel;
/**
 * 产品实体
 * @desc 产品实体
 * @uses \app\common\entity\ProductEntity
 */
class ProductEntity extends Entity
{
    protected function getOptions(): array
    {
        return [
            'modelClass' => ProductModel::class, 
        ];
    }
    /**
     * @title 管理端产品列表
     * @desc 管理端产品列表
     * @author zhaoyj
     * @version v1
     */
    public function listForAdmin(array $params): array
    {
        $query = $this->model()->db()
            ->alias('p')
            ->leftJoin('product_group pg', 'pg.id = p.product_group_id')
            ->leftJoin('server_group sg', 'sg.id = p.server_group_id')
            ->field([
                'p.id', 'p.name', 'p.type', 'p.hidden', 'p.stock',
                'p.billing_cycle', 'p.price', 'p.order',
                'p.create_time', 'p.update_time',
                'pg.name as group_name', 'sg.name as server_group_name',
            ]);

        $this->applyAdminFilters($query, $params);

        $count = (clone $query)->count();
        $list = $query
            ->order('p.order', 'asc')
            ->order('p.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 客户端产品列表
     * @desc 客户端产品列表
     * @author zhaoyj
     * @version v1
     */
    public function listForClient(array $params): array
    {
        $query = $this->model()->db()
            ->alias('p')
            ->leftJoin('product_group pg', 'pg.id = p.product_group_id')
            ->field([
                'p.id', 'p.name', 'p.description', 'p.type',
                'p.billing_cycle', 'p.price', 'p.stock',
                'pg.name as group_name',
            ])
            ->where('p.hidden', 0);

        if (!empty($params['product_group_id'])) {
            $query->where('p.product_group_id', (int)$params['product_group_id']);
        }
        if (!empty($params['keywords'])) {
            $query->where('p.name', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('p.order', 'asc')
            ->order('p.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 产品详情
     * @desc 产品详情
     * @author zhaoyj
     * @version v1
     */
    public function detail(int $id, bool $publicOnly = false): ?array
    {
        $query = $this->model()->db()
            ->alias('p')
            ->leftJoin('product_group pg', 'pg.id = p.product_group_id')
            ->leftJoin('server_group sg', 'sg.id = p.server_group_id')
            ->field([
                'p.*',
                'pg.name as group_name', 'sg.name as server_group_name',
            ])
            ->where('p.id', $id);

        if ($publicOnly) {
            $query->where('p.hidden', 0);
        }

        $product = $query->find();

        if (!$product) {
            return null;
        }

        $product = $product->toArray();

        // 批量加载配置选项
        $product['config_options'] = $this->loadConfigOptions($id);

        return $product;
    }

    /**
     * 加载配置选项及子项
     */
    private function loadConfigOptions(int $productId): array
    {
        $options = $this->model()->db()->name('config_option')
            ->where('product_id', $productId)
            ->order('order', 'asc')
            ->select()
            ->toArray();

        if (empty($options)) {
            return [];
        }

        $optionIds = array_column($options, 'id');
        $subs = $this->model()->db()->name('config_option_sub')
            ->whereIn('config_option_id', $optionIds)
            ->order('order', 'asc')
            ->select()
            ->toArray();

        // 按 config_option_id 分组
        $subMap = [];
        foreach ($subs as $sub) {
            $subMap[$sub['config_option_id']][] = $sub;
        }

        foreach ($options as &$option) {
            $option['subs'] = $subMap[$option['id']] ?? [];
        }

        return $options;
    }

    private function applyAdminFilters($query, array $params): void
    {
        if (!empty($params['product_group_id'])) {
            $query->where('p.product_group_id', (int)$params['product_group_id']);
        }
        if (isset($params['hidden']) && $params['hidden'] !== '') {
            $query->where('p.hidden', (int)$params['hidden']);
        }
        if (!empty($params['keywords'])) {
            $query->where('p.name', 'like', '%' . $params['keywords'] . '%');
        }
        if (!empty($params['type'])) {
            $query->where('p.type', $params['type']);
        }
    }
}