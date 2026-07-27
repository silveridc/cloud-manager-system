<?php
namespace app\admin\controller;

use app\admin\logic\ServerLogic;

/**
 * 服务器控制器
 * @desc 服务器控制器
 * @uses \app\admin\controller\ServerController
 */
class ServerController extends AdminBaseController
{
    protected ServerLogic $serverLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->serverLogic = app(ServerLogic::class);
    }

    /**
     * @title 获取服务器列表
     * @desc 获取服务器列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetServerList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->serverLogic->GetServerList($params);
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
     * @title 获取服务器详情
     * @desc 获取服务器详情
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetServerInfo(int $id)
    {
        $data = $this->serverLogic->GetServerInfo($id);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('server_not_found'),
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
     * @title 创建服务器
     * @desc 创建服务器
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CreateServer()
    {
        $params = $this->request->param();
        $id = $this->serverLogic->CreateServer($params);
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
     * @title 修改服务器
     * @desc 修改服务器
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateServer(int $id)
    {
        $params = $this->request->param();
        $result = $this->serverLogic->UpdateServer($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除服务器
     * @desc 删除服务器
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/server/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteServer(int $id)
    {
        $result = $this->serverLogic->DeleteServer($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}