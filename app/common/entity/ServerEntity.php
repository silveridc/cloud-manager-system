<?php
namespace app\common\entity;

use think\Entity;
use app\common\model\ServerModel;
/**
 * 服务器实体
 * @desc 服务器实体
 * @uses \app\common\entity\ServerEntity
 */
class ServerEntity extends Entity
{
    protected function getOptions(): array
    {
        return [
            'modelClass' => ServerModel::class,
        ];
    }
    /**
     * @title 管理端服务器列表
     * @desc 管理端服务器列表
     * @author zhaoyj
     * @version v1
     */
    public function listForAdmin(array $params): array
    {
        $query = $this->model()->db()
            ->alias('s')
            ->leftJoin('server_group sg', 'sg.id = s.server_group_id')
            ->field([
                's.id', 's.name', 's.hostname', 's.ip', 's.port',
                's.status', 's.server_group_id', 's.module', 's.create_time',
                'sg.name as group_name',
            ]);

        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->whereOr('s.name', 'like', $kw)
                    ->whereOr('s.hostname', 'like', $kw)
                    ->whereOr('s.ip', 'like', $kw);
            });
        }
        if (!empty($params['server_group_id'])) {
            $query->where('s.server_group_id', (int)$params['server_group_id']);
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('s.status', (int)$params['status']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('s.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        // 批量加载产品实例数
        if ($list) {
            $serverIds = array_column($list, 'id');
            $hostCounts = $this->model()->db()->name('host')
                ->whereIn('server_id', $serverIds)
                ->where('status', '<>', 'Deleted')
                ->field('server_id, COUNT(*) as total')
                ->group('server_id')
                ->column('total', 'server_id');

            foreach ($list as &$item) {
                $item['host_num'] = $hostCounts[$item['id']] ?? 0;
            }
        }

        return ['list' => $list, 'count' => $count];
    }
}