<?php
namespace app\admin\controller;

use app\admin\logic\ServerGroupLogic;

/**
 * 服务器分组控制器
 * @desc 服务器分组控制器
 * @uses \app\admin\controller\ServerGroupController
 */
class ServerGroupController extends AdminBaseController
{
    protected ServerGroupLogic $serverGroupLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->serverGroupLogic = app(ServerGroupLogic::class);
    }

    /**
     * @title 获取服务器分组列表
     * @desc 获取服务器分组列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server_group
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetServerGroupList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->serverGroupLogic->GetServerGroupList($params);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取服务器分组详情
     * @desc 获取服务器分组详情
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server_group/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetServerGroupInfo(int $id)
    {
        $data = $this->serverGroupLogic->GetServerGroupInfo($id);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('server_group_not_found'),
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
     * @title 创建服务器分组
     * @desc 创建服务器分组
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server_group
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CreateServerGroup()
    {
        $params = $this->request->param();
        $id = $this->serverGroupLogic->CreateServerGroup($params);
        if (is_array($id) && isset($id['status'])) return json($id, $id['status']);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $id,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 修改服务器分组
     * @desc 修改服务器分组
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server_group/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateServerGroup(int $id)
    {
        $params = $this->request->param();
        $result = $this->serverGroupLogic->UpdateServerGroup($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除服务器分组
     * @desc 删除服务器分组
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server_group/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteServerGroup(int $id)
    {
        $result = $this->serverGroupLogic->DeleteServerGroup($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}