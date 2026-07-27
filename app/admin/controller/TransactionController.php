<?php
namespace app\admin\controller;

use app\admin\logic\TransactionLogic;

/**
 * 交易记录控制器
 * @desc 交易记录控制器
 * @uses \app\admin\controller\TransactionController
 */
class TransactionController extends AdminBaseController
{
    protected TransactionLogic $transactionLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->transactionLogic = app(TransactionLogic::class);
    }

    /**
     * @title 获取交易记录列表
     * @desc 获取交易记录列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/transaction
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetTransactionList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->transactionLogic->GetTransactionList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }
}