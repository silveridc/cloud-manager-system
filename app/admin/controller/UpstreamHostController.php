<?php
namespace app\admin\controller;

use app\common\logic\UpstreamLogic;

/**
 * 上游主机控制器
 * @desc 上游主机控制器
 * @uses \app\admin\controller\UpstreamHostController
 */
class UpstreamHostController extends AdminBaseController
{
    protected UpstreamLogic $upstreamLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->upstreamLogic = app(UpstreamLogic::class);
    }

    /**
     * @title 获取上游主机列表
     * @desc 获取上游主机列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/upstream_host
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetUpstreamHostList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->upstreamLogic->getHostList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }
}