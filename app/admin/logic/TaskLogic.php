<?php
namespace app\admin\logic;

use app\common\model\TaskModel;

/**
 * 任务逻辑
 * @desc 任务逻辑
 * @uses \app\admin\logic\TaskLogic
 */
class TaskLogic
{
    /**
     * 获取任务列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function GetTaskList(array $params): array
    {
        $TaskModel = new TaskModel();
        $query = $TaskModel;

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (!empty($params['type'])) {
            $query->where('type', $params['type']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 重试任务
     * @author zhaoyj
     * @version v1
     * @param int $id - 任务ID
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function RetryTask(int $id)
    {
        $TaskModel = new TaskModel();
        $task = $TaskModel->where('id', $id)->find();
        if (!$task) {
            return [
                'status' => 404,
                'messages' => lang('task_not_found'),
                'time' => time(),
            ];
        }
        if ($task['status'] !== 'Failed') {
            return [
                'status' => 400,
                'messages' => lang('task_cannot_retry'),
                'time' => time(),
            ];
        }

        (new TaskModel())->where('id', $id)->update([
            'status' => 'Wait',
            'update_time' => time(),
        ]);
    }
}