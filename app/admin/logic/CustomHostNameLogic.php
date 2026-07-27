<?php
namespace app\admin\logic;

use app\admin\model\CustomHostNameModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 自定义主机名逻辑
 * @desc 自定义主机名逻辑
 * @uses \app\admin\logic\CustomHostNameLogic
 */
class CustomHostNameLogic
{
    /**
     * 获取自定义主机名称列表
     * @author zhaoyj
     * @version v1
     * @param array $params ['limit'] - 每页条数(默认20)
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList(array $params): array
    {
        $CustomHostNameModel = new CustomHostNameModel();

        $query = $CustomHostNameModel;

        if (!empty($params['keywords'])) {
            $query = $query->where('name', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建自定义主机名称
     * @author zhaoyj
     * @version v1
     * @param array $params ['order'] - 排序值(默认0)
     * @return int - 新创建记录的ID
     */
    public function create(array $params): int
    {
        $CustomHostNameModel = new CustomHostNameModel();

        return (int)$CustomHostNameModel->insertGetId([
            'name' => $params['name'],
            'order' => $params['order'] ?? 0,
        ]);
    }

    /**
     * 更新自定义主机名称
     * @author zhaoyj
     * @version v1
     * @param int $id - 记录ID
     * @param array $params ['order'] - 排序值
     * @return array|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $params)
    {
        $CustomHostNameModel = new CustomHostNameModel();

        $record = $CustomHostNameModel->where('id', $id)->find();
        if (!$record) {
            return [
                'status' => 404,
                'messages' => lang('data_not_found'),
                'time' => time(),
            ];
        }

        $updateData = [];
        if (isset($params['name'])) {
            $updateData['name'] = $params['name'];
        }
        if (isset($params['order'])) {
            $updateData['order'] = $params['order'];
        }

        if ($updateData) {
            $CustomHostNameModel->where('id', $id)->update($updateData);
        }
    }

    /**
     * 删除自定义主机名称
     * @author zhaoyj
     * @version v1
     * @param int $id - 记录ID
     * @return void
     */
    public function delete(int $id): void
    {
        $CustomHostNameModel = new CustomHostNameModel();

        $CustomHostNameModel->where('id', $id)->delete();
    }
}