<?php
namespace app\admin\controller;

use app\admin\logic\ClientCreditLogic;

/**
 * 客户余额控制器
 * @desc 客户余额控制器
 * @uses \app\admin\controller\ClientCreditController
 */
class ClientCreditController extends AdminBaseController
{
    protected ClientCreditLogic $clientCreditLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->clientCreditLogic = app(ClientCreditLogic::class);
    }

    /**
     * @title 获取客户余额记录列表
     * @desc 获取客户余额记录列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client_credit
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetClientCreditList(int $clientId)
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->clientCreditLogic->getRecords($clientId, $params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 客户余额充值
     * @desc 客户余额充值
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client_credit/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function RechargeClientCredit()
    {
        $clientId = (int)$this->request->param('client_id');
        $amount = (float)$this->request->param('amount');
        $notes = $this->request->param('notes', '');

        if ($clientId <= 0) {
            return json(['status' => 400, 'messages' => lang('invalid_param') ?: '参数错误', 'time' => time()], 400);
        }
        if ($amount <= 0 || $amount > 1000000) {
            return json(['status' => 400, 'messages' => lang('invalid_amount') ?: '金额无效', 'time' => time()], 400);
        }

        $result = $this->clientCreditLogic->updateCredit($clientId, $amount, 'recharge', $notes);
        if (isset($result['status']) && $result['status'] !== 200) {
            return json($result, $result['status']);
        }
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 客户余额扣减
     * @desc 客户余额扣减
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/client_credit/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeductClientCredit()
    {
        $clientId = (int)$this->request->param('client_id');
        $amount = (float)$this->request->param('amount');
        $notes = $this->request->param('notes', '');

        if ($clientId <= 0) {
            return json(['status' => 400, 'messages' => lang('invalid_param') ?: '参数错误', 'time' => time()], 400);
        }
        if ($amount <= 0 || $amount > 1000000) {
            return json(['status' => 400, 'messages' => lang('invalid_amount') ?: '金额无效', 'time' => time()], 400);
        }

        $result = $this->clientCreditLogic->updateCredit($clientId, -abs($amount), 'deduct', $notes);
        if (isset($result['status']) && $result['status'] !== 200) {
            return json($result, $result['status']);
        }
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}