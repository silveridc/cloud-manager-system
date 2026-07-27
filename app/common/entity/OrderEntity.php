<?php
namespace app\common\entity;

use think\Entity;
use app\common\model\OrderModel;
/**
 * 订单实体
 * @desc 订单实体
 * @uses \app\common\entity\OrderEntity
 */
class OrderEntity extends Entity
{
    protected function getOptions(): array
    {
        return [
            'modelClass' => OrderModel::class, 
        ];
    }
    /**
     * @title 管理端订单列表
     * @desc 管理端订单列表
     * @author zhaoyj
     * @version v1
     */
    public function listForAdmin(array $params): array
    {
        $query = $this->model()->db()
            ->alias('o')
            ->leftJoin('client c', 'c.id = o.client_id')
            ->leftJoin('host h', 'h.id = o.host_id')
            ->leftJoin('product p', 'p.id = h.product_id')
            ->field([
                'o.id', 'o.type', 'o.status', 'o.amount', 'o.credit_amount',
                'o.create_time', 'o.pay_time',
                'c.username as client_name', 'c.email as client_email',
                'h.name as host_name', 'p.name as product_name',
            ]);

        $this->applyFilters($query, $params);

        $count = (clone $query)->count();
        $list = $query
            ->order('o.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 客户端订单列表
     * @desc 客户端订单列表
     * @author zhaoyj
     * @version v1
     */
    public function listForClient(int $clientId, array $params): array
    {
        $query = $this->model()->db()
            ->alias('o')
            ->leftJoin('host h', 'h.id = o.host_id')
            ->leftJoin('product p', 'p.id = h.product_id')
            ->field([
                'o.id', 'o.type', 'o.status', 'o.amount', 'o.credit_amount',
                'o.create_time', 'o.pay_time',
                'h.name as host_name', 'p.name as product_name',
            ])
            ->where('o.client_id', $clientId);

        if (!empty($params['status'])) {
            $query->where('o.status', $params['status']);
        }
        if (!empty($params['type'])) {
            $query->where('o.type', $params['type']);
        }
        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->whereOr('o.id', 'like', $kw)
                    ->whereOr('h.name', 'like', $kw);
            });
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('o.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 订单详情
     * @desc 订单详情
     * @author zhaoyj
     * @version v1
     */
    public function detail(int $id, ?int $clientId = null): ?array
    {
        $query = $this->model()->db()
            ->alias('o')
            ->leftJoin('client c', 'c.id = o.client_id')
            ->leftJoin('host h', 'h.id = o.host_id')
            ->leftJoin('product p', 'p.id = h.product_id')
            ->field([
                'o.*',
                'c.username as client_name', 'c.email as client_email',
                'h.name as host_name', 'p.name as product_name',
            ])
            ->where('o.id', $id);

        if ($clientId !== null) {
            $query->where('o.client_id', $clientId);
        }

        $order = $query->find();
        if (!$order) {
            return null;
        }

        $order = $order->toArray();

        // 批量加载订单明细
        $order['items'] = $this->model()->db()->name('order_item')
            ->alias('oi')
            ->leftJoin('product p', 'p.id = oi.product_id')
            ->field(['oi.*', 'p.name as product_name'])
            ->where('oi.order_id', $id)
            ->select()
            ->toArray();

        return $order;
    }

    /**
     * 管理端过滤条件
     */
    private function applyFilters($query, array $params): void
    {
        if (!empty($params['status'])) {
            $query->where('o.status', $params['status']);
        }
        if (!empty($params['type'])) {
            $query->where('o.type', $params['type']);
        }
        if (!empty($params['client_id'])) {
            $query->where('o.client_id', (int)$params['client_id']);
        }
        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->whereOr('o.id', 'like', $kw)
                    ->whereOr('c.username', 'like', $kw)
                    ->whereOr('c.email', 'like', $kw);
            });
        }
        if (!empty($params['start_time'])) {
            $query->where('o.create_time', '>=', strtotime($params['start_time']));
        }
        if (!empty($params['end_time'])) {
            $query->where('o.create_time', '<=', strtotime($params['end_time']));
        }
    }
}