<?php

namespace app\home\controller;

use app\home\logic\OrderLogic;

/**
 * 客户端订单控制器
 * @desc 客户端订单控制器
 * @uses \app\home\controller\OrderController
 */
class OrderController extends HomeBaseController
{
    protected OrderLogic $orderLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->orderLogic = app(OrderLogic::class);
    }

    /**
     * @title 获取订单列表
     * @desc 获取订单列表
     * @author zhaoyj
     * @version v1
     * @url /console/v1/order
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return array data.list - 订单列表
     * @return int data.count - 总数
     * @return int time - 时间戳
     */
    public function GetOrderList()
    {
        $params = array_merge($this->request->param(), $this->paginationParams());
        $data = $this->orderLogic->getClientList($this->clientId(), $params);
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
     * @title 获取订单详情
     * @desc 获取订单详情
     * @author zhaoyj
     * @version v1
     * @url /console/v1/order/:id
     * @method get
     * @param int id - 订单ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return object data - 订单详情
     * @return int time - 时间戳
     */
    public function GetOrderInfo(int $id)
    {
        $data = $this->orderLogic->GetOrderInfo($id, $this->clientId());
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('order_not_found'),
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
     * @title 取消订单
     * @desc 取消订单
     * @author zhaoyj
     * @version v1
     * @url /console/v1/order/:id/cancel
     * @method put
     * @param int id - 订单ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CancelOrder(int $id)
    {
        $result = $this->orderLogic->CancelOrder($id, $this->clientId());
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}