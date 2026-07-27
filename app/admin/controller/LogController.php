<?php
namespace app\admin\controller;

use app\admin\logic\SystemLogLogic;

/**
 * 日志控制器
 * @desc 日志控制器
 * @uses \app\admin\controller\LogController
 */
class LogController extends AdminBaseController
{
    protected SystemLogLogic $logLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->logLogic = app(SystemLogLogic::class);
    }

    /**
     * @title 获取系统日志列表
     * @desc 获取系统日志列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/log
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetLogList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->logLogic->GetLogList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }
}