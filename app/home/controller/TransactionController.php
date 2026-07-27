<?php

namespace app\home\controller;

use app\home\logic\ClientCreditLogic;

/**
 * 交易记录控制器
 * @desc 交易记录控制器
 * @uses \app\home\controller\TransactionController
 */
class TransactionController extends HomeBaseController
{
    protected ClientCreditLogic $clientCreditLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->clientCreditLogic = app(ClientCreditLogic::class);
    }

    /**
     * @title 获取交易记录列表
     * @desc 获取交易记录列表
     * @author zhaoyj
     * @version v1
     * @url /console/v1/transaction
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetTransactionList()
    {
        $params = array_merge($this->request->param(), $this->paginationParams());
        $data = $this->clientCreditLogic->getRecords($this->clientId(), $params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }
}