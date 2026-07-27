<?php
namespace app\admin\controller;

use app\admin\logic\ClientRecordLogic;

/**
 * 客户记录控制器
 * @desc 客户记录控制器
 * @uses \app\admin\controller\ClientRecordController
 */
class ClientRecordController extends AdminBaseController
{
    protected ClientRecordLogic $clientRecordLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->clientRecordLogic = app(ClientRecordLogic::class);
    }

    /**
     * @title 获取客户记录列表
     * @desc 获取客户记录列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client_record
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetClientRecordList(int $clientId)
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->clientRecordLogic->GetClientRecordList($clientId, $params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 创建客户记录
     * @desc 创建客户记录
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client_record
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CreateClientRecord()
    {
        $params = $this->request->param();
        $clientId = (int)$params['client_id'];
        $id = $this->clientRecordLogic->CreateClientRecord($clientId, $params, $this->adminId());
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
     * @title 删除客户记录
     * @desc 删除客户记录
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client_record/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteClientRecord(int $id)
    {
        $clientId = (int)$this->request->param('client_id');
        $result = $this->clientRecordLogic->DeleteClientRecord($clientId, $id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}