<?php
namespace app\admin\controller;

use app\admin\logic\OrderRecordLogic;

/**
 * 订单记录控制器
 * @desc 订单记录控制器
 * @uses \app\admin\controller\OrderRecordController
 */
class OrderRecordController extends AdminBaseController
{
    protected OrderRecordLogic $orderRecordLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->orderRecordLogic = app(OrderRecordLogic::class);
    }

    /**
     * @title 获取订单记录列表
     * @desc 获取订单记录列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/order_record
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetOrderRecordList(int $orderId)
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->orderRecordLogic->GetOrderRecordList($orderId, $params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 创建订单记录
     * @desc 创建订单记录
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/order_record
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CreateOrderRecord()
    {
        $params = $this->request->param();
        $orderId = (int)$params['order_id'];
        $id = $this->orderRecordLogic->CreateOrderRecord($orderId, $params, $this->adminId());
        if (is_array($id) && isset($id['status'])) return json($id, $id['status']);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $id,
            'time' => time(),
        ];
        return json($result);
    }
}