<?php
namespace app\admin\controller;

use app\admin\logic\TaskLogic;

/**
 * 任务控制器
 * @desc 任务控制器
 * @uses \app\admin\controller\TaskController
 */
class TaskController extends AdminBaseController
{
    protected TaskLogic $taskLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->taskLogic = app(TaskLogic::class);
    }

    /**
     * @title 获取任务列表
     * @desc 获取任务列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/task
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetTaskList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->taskLogic->GetTaskList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Retry
     * @desc Retry
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/task/:id
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function RetryTask(int $id)
    {
        $result = $this->taskLogic->RetryTask($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}