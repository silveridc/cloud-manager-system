<?php
namespace app\admin\logic;

use app\common\model\HostModel;
use app\common\model\ServerModel;
use think\facade\Event;

/**
 * 服务器逻辑
 * @uses \app\admin\logic\ServerLogic
 */
class ServerLogic
{
    /**
     * 获取服务器列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function GetServerList(array $params): array
    {
        $ServerModel = new ServerModel();
        $HostModel = new HostModel();

        $query = $ServerModel->alias('s')
            ->leftJoin('server_group sg', 'sg.id = s.server_group_id')
            ->field([
                's.id', 's.name', 's.hostname', 's.ip', 's.port',
                's.status', 's.server_group_id', 's.create_time',
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
            ->select()->toArray();

        // 批量加载产品实例数
        if ($list) {
            $serverIds = array_column($list, 'id');
            $hostCounts = $HostModel
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

    /**
     * 获取服务器详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 服务器ID
     * @return array|null
     */
    public function GetServerInfo(int $id): ?array
    {
        $ServerModel = new ServerModel();

        // Note: password is excluded from API response to prevent information disclosure.
        // TODO: Implement AES encryption for password storage.
        $server = $ServerModel->alias('s')
            ->leftJoin('server_group sg', 'sg.id = s.server_group_id')
            ->field([
                's.id', 's.name', 's.hostname', 's.ip', 's.port',
                's.username', 's.server_group_id', 's.module',
                's.status', 's.create_time', 's.update_time',
                'sg.name as group_name',
            ])
            ->where('s.id', $id)
            ->find();

        return $server ? $server->toArray() : null;
    }

    /**
     * 创建服务器
     * @author zhaoyj
     * @version v1
     * @param array $params - 服务器参数
     * @return int - 新建服务器ID
     */
    public function CreateServer(array $params): int
    {
        $server = new ServerModel();
        $server->save([
            'name' => $params['name'],
            'hostname' => $params['hostname'] ?? '',
            'ip' => $params['ip'] ?? '',
            'port' => $params['port'] ?? 22,
            'username' => $params['username'] ?? '',
            'password' => $params['password'] ?? '',
            'server_group_id' => $params['server_group_id'] ?? 0,
            'module' => $params['module'] ?? '',
            'status' => $params['status'] ?? 1,
        ]);

        Event::trigger('after_server_create', ['server_id' => $server->id]);
        return (int)$server->id;
    }

    /**
     * 更新服务器
     * @author zhaoyj
     * @version v1
     * @param int $id - 服务器ID
     * @param array $params - 服务器参数
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function UpdateServer(int $id, array $params)
    {
        $server = ServerModel::find($id);
        if (!$server) {
            return [
                'status' => 404,
                'messages' => lang('server_not_found'),
                'time' => time(),
            ];
        }

        $allowFields = ['name', 'hostname', 'ip', 'port', 'username', 'password', 'server_group_id', 'module', 'status'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if ($updateData) {
            $server->save($updateData);
        }
    }

    /**
     * 删除服务器
     * @author zhaoyj
     * @version v1
     * @param int $id - 服务器ID
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function DeleteServer(int $id)
    {
        $HostModel = new HostModel();

        $server = ServerModel::find($id);
        if (!$server) {
            return [
                'status' => 404,
                'messages' => lang('server_not_found'),
                'time' => time(),
            ];
        }

        $hostCount = $HostModel
            ->where('server_id', $id)
            ->where('status', '<>', 'Deleted')
            ->count();

        if ($hostCount > 0) {
            return [
                'status' => 400,
                'messages' => lang('server_has_active_hosts'),
                'time' => time(),
            ];
        }

        $server->delete();
    }
}