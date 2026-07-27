<?php
namespace app\admin\controller;

use app\admin\logic\HostLogic;

/**
 * 管理端产品实例控制器
 * @desc 管理端产品实例控制器
 * @uses \app\admin\controller\HostController
 */
class HostController extends AdminBaseController
{
    protected HostLogic $hostLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->hostLogic = app(HostLogic::class);
    }

    /**
     * @title 获取产品实例列表
     * @desc 获取产品实例列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/host
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return array data.list - 实例列表
     * @return int data.count - 总数
     * @return int time - 时间戳
     */
    public function GetHostList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->hostLogic->GetHostList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取产品实例详情
     * @desc 获取产品实例详情
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/host/:id
     * @method get
     * @param int id - 实例ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return object data - 实例详情
     * @return int time - 时间戳
     */
    public function GetHostInfo(int $id)
    {
        $data = $this->hostLogic->GetHostInfo($id);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('host_not_found'),
                'time' => time(),
            ];
            return json($result, 404);
        }
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 暂停产品实例
     * @desc 暂停产品实例
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/host/:id/suspend
     * @method post
     * @param int id - 实例ID required
     * @param string reason - 暂停原因
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function SuspendHost(int $id)
    {
        $reason = $this->request->param('reason', '');
        $result = $this->hostLogic->SuspendHost($id, $reason);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 解除暂停
     * @desc 解除暂停
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/host/:id/unsuspend
     * @method post
     * @param int id - 实例ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UnsuspendHost(int $id)
    {
        $result = $this->hostLogic->UnsuspendHost($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 终止产品实例
     * @desc 终止产品实例
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/host/:id/terminate
     * @method post
     * @param int id - 实例ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function TerminateHost(int $id)
    {
        $result = $this->hostLogic->TerminateHost($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 更新实例备注
     * @desc 更新实例备注
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/host/:id/notes
     * @method put
     * @param int id - 实例ID required
     * @param string notes - 备注内容
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateHostNotes(int $id)
    {
        $notes = $this->request->param('notes', '');
        $result = $this->hostLogic->UpdateHostNotes($id, $notes);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}
