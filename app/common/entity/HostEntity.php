<?php
namespace app\common\entity;

use think\Entity;
use app\common\model\HostModel;
/**
 * 主机实例实体
 * @desc 主机实例实体
 * @uses \app\common\entity\HostEntity
 */
class HostEntity extends Entity
{
    protected function getOptions(): array
    {
        return [
            'modelClass' => HostModel::class, 
        ];
    }
    /**
     * @title 管理端产品实例列表
     * @desc 管理端产品实例列表
     * @author zhaoyj
     * @version v1
     */
    public function listForAdmin(array $params): array
    {
        $query = $this->model()->db()
            ->alias('h')
            ->leftJoin('client c', 'c.id = h.client_id')
            ->leftJoin('product p', 'p.id = h.product_id')
            ->leftJoin('server s', 's.id = h.server_id')
            ->field([
                'h.id', 'h.name', 'h.status', 'h.billing_cycle',
                'h.first_payment_amount', 'h.renew_amount',
                'h.active_time', 'h.due_time', 'h.create_time',
                'c.username as client_name', 'c.email as client_email',
                'p.name as product_name', 's.name as server_name',
            ])
            ->where('h.status', '<>', 'Deleted');

        $this->applyAdminFilters($query, $params);

        $count = (clone $query)->count();
        $list = $query
            ->order('h.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        // 批量加载 IP 信息
        if ($list) {
            $hostIds = array_column($list, 'id');
            $ips = $this->batchLoadIps($hostIds);
            foreach ($list as &$item) {
                $item['ips'] = $ips[$item['id']] ?? [];
            }
        }

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 客户端产品实例列表
     * @desc 客户端产品实例列表
     * @author zhaoyj
     * @version v1
     */
    public function listForClient(int $clientId, array $params): array
    {
        $query = $this->model()->db()
            ->alias('h')
            ->leftJoin('product p', 'p.id = h.product_id')
            ->field([
                'h.id', 'h.name', 'h.status', 'h.billing_cycle',
                'h.first_payment_amount', 'h.renew_amount',
                'h.active_time', 'h.due_time', 'h.create_time',
                'p.name as product_name',
            ])
            ->where('h.client_id', $clientId)
            ->where('h.status', '<>', 'Deleted');

        if (!empty($params['status'])) {
            $query->where('h.status', $params['status']);
        }
        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->whereOr('h.name', 'like', $kw)
                    ->whereOr('p.name', 'like', $kw);
            });
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('h.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 产品实例详情
     * @desc 产品实例详情
     * @author zhaoyj
     * @version v1
     */
    public function detail(int $id, ?int $clientId = null): ?array
    {
        $query = $this->model()->db()
            ->alias('h')
            ->leftJoin('client c', 'c.id = h.client_id')
            ->leftJoin('product p', 'p.id = h.product_id')
            ->leftJoin('server s', 's.id = h.server_id')
            ->field([
                'h.*',
                'c.username as client_name', 'c.email as client_email',
                'p.name as product_name', 's.name as server_name',
            ])
            ->where('h.id', $id)
            ->where('h.status', '<>', 'Deleted');

        if ($clientId !== null) {
            $query->where('h.client_id', $clientId);
        }

        $host = $query->find();
        if (!$host) {
            return null;
        }

        $host = $host->toArray();

        // 加载 IP
        $ips = $this->batchLoadIps([$id]);
        $host['ips'] = $ips[$id] ?? [];

        return $host;
    }

    private function applyAdminFilters($query, array $params): void
    {
        if (!empty($params['status'])) {
            $query->where('h.status', $params['status']);
        }
        if (!empty($params['client_id'])) {
            $query->where('h.client_id', (int)$params['client_id']);
        }
        if (!empty($params['product_id'])) {
            $query->where('h.product_id', (int)$params['product_id']);
        }
        if (!empty($params['server_id'])) {
            $query->where('h.server_id', (int)$params['server_id']);
        }
        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->whereOr('h.name', 'like', $kw)
                    ->whereOr('c.username', 'like', $kw)
                    ->whereOr('c.email', 'like', $kw);
            });
        }
    }

    /**
     * 批量加载 IP（防止 N+1）
     */
    private function batchLoadIps(array $hostIds): array
    {
        if (empty($hostIds)) {
            return [];
        }

        $rows = $this->model()->db()->name('host_ip')
            ->whereIn('host_id', $hostIds)
            ->field(['host_id', 'ip', 'subnet_mask', 'gateway'])
            ->select()
            ->toArray();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['host_id']][] = $row;
        }
        return $map;
    }
}