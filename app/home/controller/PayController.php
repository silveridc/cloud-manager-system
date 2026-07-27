<?php

namespace app\home\controller;

use app\home\logic\PayLogic;

/**
 * 支付控制器
 * @desc 支付控制器
 * @uses \app\home\controller\PayController
 */
class PayController extends HomeBaseController
{
    protected PayLogic $payLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->payLogic = app(PayLogic::class);
    }

    /**
     * @title Gateway List
     * @desc Gateway List
     * @author zhaoyj
     * @version v1
     * @url /console/v1/pay/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function gatewayList()
    {
        $data = $this->payLogic->getGatewayList();
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
     * @title Pay
     * @desc Pay
     * @author zhaoyj
     * @version v1
     * @url /console/v1/pay/:id
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function pay()
    {
        $orderId = (int)$this->request->param('order_id');
        $gateway = $this->request->param('gateway', '');
        $data = $this->payLogic->pay($this->clientId(), $orderId, $gateway);
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
     * @title Credit Pay
     * @desc Credit Pay
     * @author zhaoyj
     * @version v1
     * @url /console/v1/pay/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function creditPay()
    {
        $orderId = (int)$this->request->param('order_id');
        $result = $this->payLogic->creditPay($this->clientId(), $orderId);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}