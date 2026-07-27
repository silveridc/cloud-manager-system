<?php

namespace app\home\entity;

use think\Entity;

/**
 * 客户实体
 * @desc 客户实体
 * @uses \app\home\entity\ClientEntity
 */
class ClientEntity extends Entity
{
    /**
     * @title 分页列表（管理端）
     * @desc 分页列表（管理端）
     * @author zhaoyj
     * @version v1
     */
    public function listForAdmin(array $params): array
    {
        $query = $this->model()->db()
            ->alias('c')
            ->leftJoin('country co', 'co.id = c.country_id')
            ->field([
                'c.id', 'c.username', 'c.email', 'c.phone_code', 'c.phone',
                'c.status', 'c.credit', 'c.company', 'c.language',
                'c.create_time', 'c.notes', 'co.name_zh as country_name',
            ]);

        // 条件过滤
        $this->applyAdminFilters($query, $params);

        $count = (clone $query)->count();
        $list = $query
            ->order('c.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, min($params['limit'] ?? 20, 100))
            ->select()
            ->toArray();

        // 批量加载产品实例数量（防止 N+1）
        if ($list) {
            $clientIds = array_column($list, 'id');
            $hostCounts = $this->batchHostCounts($clientIds);
            foreach ($list as &$item) {
                $item['host_num'] = $hostCounts[$item['id']]['total'] ?? 0;
                $item['host_active_num'] = $hostCounts[$item['id']]['active'] ?? 0;
            }
        }

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 客户详情
     * @desc 客户详情
     * @author zhaoyj
     * @version v1
     */
    public function detail(int $id): ?array
    {
        $client = $this->model()->db()
            ->alias('c')
            ->leftJoin('country co', 'co.id = c.country_id')
            ->field([
                'c.id', 'c.username', 'c.email', 'c.phone_code', 'c.phone',
                'c.status', 'c.credit', 'c.company', 'c.address', 'c.language',
                'c.notes', 'c.create_time', 'c.update_time',
                'co.name_zh as country_name',
            ])
            ->where('c.id', $id)
            ->find();

        if (!$client) {
            return null;
        }

        $client = $client->toArray();

        // 加载产品实例统计
        $counts = $this->batchHostCounts([$id]);
        $client['host_num'] = $counts[$id]['total'] ?? 0;
        $client['host_active_num'] = $counts[$id]['active'] ?? 0;

        return $client;
    }

    /**
     * @title 客户端我的信息
     * @desc 客户端我的信息
     * @author zhaoyj
     * @version v1
     */
    public function profile(int $clientId): ?array
    {
        return $this->model()->db()
            ->alias('c')
            ->leftJoin('country co', 'co.id = c.country_id')
            ->field([
                'c.id', 'c.username', 'c.email', 'c.phone_code', 'c.phone',
                'c.credit', 'c.company', 'c.address', 'c.language',
                'co.name_zh as country_name', 'c.create_time',
            ])
            ->where('c.id', $clientId)
            ->find()
            ?->toArray();
    }

    /**
     * 管理端筛选条件
     */
    private function applyAdminFilters($query, array $params): void
    {
        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->whereOr('c.username', 'like', $kw)
                    ->whereOr('c.email', 'like', $kw)
                    ->whereOr('c.phone', 'like', $kw)
                    ->whereOr('c.company', 'like', $kw);
            });
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('c.status', (int)$params['status']);
        }
        if (!empty($params['country_id'])) {
            $query->where('c.country_id', (int)$params['country_id']);
        }
    }

    /**
     * 批量加载产品实例数（防止 N+1）
     */
    private function batchHostCounts(array $clientIds): array
    {
        if (empty($clientIds)) {
            return [];
        }

        $rows = $this->model()->db()->name('host')
            ->whereIn('client_id', $clientIds)
            ->where('status', '<>', 'Deleted')
            ->field([
                'client_id',
                'COUNT(*) as total',
                "SUM(CASE WHEN status='Active' THEN 1 ELSE 0 END) as active",
            ])
            ->group('client_id')
            ->select()
            ->toArray();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['client_id']] = $row;
        }
        return $map;
    }
}