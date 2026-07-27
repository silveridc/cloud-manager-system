<?php
namespace app\admin\controller;

use app\admin\logic\ClientCreditLogic;
use app\admin\logic\ClientLogic;
use app\admin\logic\ClientRecordLogic;
use app\admin\validate\ClientValidate;
/**
 * @title 管理员客户控制器
 * @desc 管理员客户控制器
 * @uses \app\admin\controller\ClientController
 */
class ClientController extends AdminBaseController
{
    protected ClientLogic $ClientLogic;
    protected ClientCreditLogic $CreditLogic;
    protected ClientRecordLogic $RecordLogic;
    protected ClientValidate $ClientValidate;
    protected function initialize()
    {
        parent::initialize();
        $this->ClientLogic = app(ClientLogic::class);
        $this->CreditLogic = app(ClientCreditLogic::class);
        $this->RecordLogic = app(ClientRecordLogic::class);
        $this->ClientValidate = app(ClientValidate::class);
    }

    /**
     * @title 获取客户列表
     * @desc 获取客户列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return array data.list - 客户列表
     * @return int data.count - 总数
     * @return int time - 时间戳
     */
    public function GetClientList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->ClientLogic->GetClientList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取客户详情
     * @desc 获取客户详情
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client/:id
     * @method get
     * @param int id - 客户ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return object data - 客户详情
     * @return int time - 时间戳
     */
    public function GetClientInfo(int $id)
    {
        $data = $this->ClientLogic->GetClientInfo($id);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('client_not_found'),
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
     * @title 创建客户
     * @desc 创建客户
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client
     * @method post
     * @param string username - 用户名 required
     * @param string email - 邮箱
     * @param string phone - 手机号
     * @param string password - 密码 required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int data.id - 客户ID
     * @return int time - 时间戳
     * @throws \Throwable
     */
    public function CreateClient()
    {
        $params = $this->request->param();
        if(!$this->ClientValidate->scene('create')->check($params)){
            return json(['status' => 403 , 'messages' => lang($this->ClientValidate->getError())],403);
        }
        return json($this->ClientLogic->CreateClient($params));
    }

    /**
     * @title 更新客户信息
     * @desc 更新客户信息
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client/:id
     * @method put
     * @param int id - 客户ID required
     * @param string username - 用户名
     * @param string email - 邮箱
     * @param string phone - 手机号
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     * @throws \Throwable
     */
    public function UpdateClient(int $id)
    {
        $params = $this->request->param();
        if(!$this->ClientValidate->scene('update')->check($params)){
            return json(['status' => 403 , 'messages' => lang($this->ClientValidate->getError())],403);
        }
        return json($this->ClientLogic->UpdateClient($id, $params));
    }

    /**
     * @title 删除客户
     * @desc 删除客户
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client/:id
     * @method delete
     * @param int id - 客户ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     * @throws \Throwable
     */
    public function DeleteClient(int $id)
    {
        $result = $this->ClientLogic->DeleteClient($id);
        return json($result);
    }

    /**
     * @title 客户余额充值/扣费
     * @desc 客户余额充值/扣费
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client/:id/credit
     * @method post
     * @param int id - 客户ID required
     * @param float amount - 金额 required
     * @param string type - 类型 required
     * @param string notes - 备注
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     * @throws \Throwable
     */
    public function updateCredit(int $id)
    {
        $params = $this->request->param();
        $amount = (float)($params['amount'] ?? 0);
        $type = $params['type'] ?? '';

        // 验证金额和类型
        if ($amount == 0 || abs($amount) > 1000000) {
            return json(['status' => 400, 'messages' => lang('invalid_amount') ?: '金额无效', 'time' => time()], 400);
        }
        $allowedTypes = ['recharge', 'deduct', 'refund', 'pay', 'admin'];
        if (!in_array($type, $allowedTypes)) {
            return json(['status' => 400, 'messages' => lang('invalid_param') ?: '类型无效', 'time' => time()], 400);
        }

        $result = $this->CreditLogic->updateCredit(
            $id,
            $amount,
            $type,
            $params['notes'] ?? ''
        );
        if (isset($result['status']) && $result['status'] !== 200) {
            return json($result, $result['status']);
        }
        return json([
            'status' => 200,
            'messages' => lang('success_message'),
            'time' => time(),
        ]);
    }

    /**
     * @title 获取客户余额记录
     * @desc 获取客户余额记录
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client/:id/credit
     * @method get
     * @param int id - 客户ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return array data.list - 余额记录列表
     * @return int data.count - 总数
     * @return int time - 时间戳
     * @throws \Throwable
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function creditList(int $id)
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->CreditLogic->getRecords($id, $params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 添加客户记录
     * @desc 添加客户记录
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client/:id/record
     * @method post
     * @param int id - 客户ID required
     * @param string content - 记录内容 required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int data.id - 记录ID
     * @return int time - 时间戳
     * @throws \Throwable
     */
    public function createRecord(int $id)
    {
        $params = $this->request->param();
        $recordId = $this->RecordLogic->CreateClientRecord($id, $params, $this->adminId());
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $recordId,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取客户记录列表
     * @desc 获取客户记录列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client/:id/record
     * @method get
     * @param int id - 客户ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return array data.list - 记录列表
     * @return int data.count - 总数
     * @return int time - 时间戳
     * @throws \Throwable
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function recordList(int $id)
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->RecordLogic->GetClientRecordList($id, $params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }
}