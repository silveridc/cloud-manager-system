<?php
namespace app\admin\logic;

use app\common\model\ServerGroupModel;
use app\common\model\ServerModel;

/**
 * 服务器分组逻辑
 * @uses \app\admin\logic\ServerGroupLogic
 */
class ServerGroupLogic
{
    /**
     * 获取服务器分组列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function GetServerGroupList(array $params): array
    {
        $ServerGroupModel = new ServerGroupModel();
        $ServerModel = new ServerModel();

        $query = $ServerGroupModel->db();

        if (!empty($params['keywords'])) {
            $query->where('name', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        // 批量加载服务器数
        if ($list) {
            $groupIds = array_column($list, 'id');
            $serverCounts = $ServerModel->db()
                ->whereIn('server_group_id', $groupIds)
                ->field('server_group_id, COUNT(*) as total')
                ->group('server_group_id')
                ->column('total', 'server_group_id');

            foreach ($list as &$item) {
                $item['server_num'] = $serverCounts[$item['id']] ?? 0;
            }
        }

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取服务器分组详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 分组ID
     * @return array|null
     */
    public function GetServerGroupInfo(int $id): ?array
    {
        $ServerGroupModel = new ServerGroupModel();
        return $ServerGroupModel->db()->where('id', $id)->find() ?: null;
    }

    /**
     * 创建服务器分组
     * @author zhaoyj
     * @version v1
     * @param array $params - 分组参数
     * @return int - 新建分组ID
     */
    public function CreateServerGroup(array $params): int
    {
        $group = new ServerGroupModel();
        $group->save([
            'name' => $params['name'],
        ]);
        return (int)$group->id;
    }

    /**
     * 更新服务器分组
     * @author zhaoyj
     * @version v1
     * @param int $id - 分组ID
     * @param array $params - 分组参数
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function UpdateServerGroup(int $id, array $params)
    {
        $group = ServerGroupModel::find($id);
        if (!$group) {
            return [
                'status' => 404,
                'messages' => lang('server_group_not_found'),
                'time' => time(),
            ];
        }
        $group->save(['name' => $params['name']]);
    }

    /**
     * 删除服务器分组
     * @author zhaoyj
     * @version v1
     * @param int $id - 分组ID
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function DeleteServerGroup(int $id)
    {
        $group = ServerGroupModel::find($id);
        if (!$group) {
            return [
                'status' => 404,
                'messages' => lang('server_group_not_found'),
                'time' => time(),
            ];
        }

        $ServerModel = new ServerModel();
        $serverCount = $ServerModel->db()->where('server_group_id', $id)->count();
        if ($serverCount > 0) {
            return [
                'status' => 400,
                'messages' => lang('server_group_has_servers'),
                'time' => time(),
            ];
        }

        $group->delete();
    }
}