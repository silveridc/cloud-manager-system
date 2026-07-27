<?php

namespace app\home\controller;

use app\home\logic\CartLogic;
use app\common\logic\SettlementLogic;

/**
 * 购物车控制器
 * @desc 购物车控制器
 * @uses \app\home\controller\CartController
 */
class CartController extends HomeBaseController
{
    protected CartLogic $cartLogic;
    protected SettlementLogic $settlementLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->cartLogic = app(CartLogic::class);
        $this->settlementLogic = app(SettlementLogic::class);
    }

    /**
     * @title 获取购物车列表
     * @desc 获取购物车列表
     * @author zhaoyj
     * @version v1
     * @url /console/v1/cart
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetCartList()
    {
        $data = $this->cartLogic->GetCartList($this->clientId());
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
     * @title 添加购物车
     * @desc 添加购物车
     * @author zhaoyj
     * @version v1
     * @url /console/v1/cart
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function AddToCart()
    {
        $params = $this->request->param();
        $id = $this->cartLogic->AddToCart($this->clientId(), $params);
        if (is_array($id) && isset($id['status']) && $id['status'] !== 200) return json($id, $id['status']);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $id,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 修改购物车
     * @desc 修改购物车
     * @author zhaoyj
     * @version v1
     * @url /console/v1/cart/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateCart(int $id)
    {
        $params = $this->request->param();
        $result = $this->cartLogic->UpdateCart($this->clientId(), $id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除购物车项
     * @desc 删除购物车项
     * @author zhaoyj
     * @version v1
     * @url /console/v1/cart/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteCart(int $id)
    {
        $this->cartLogic->DeleteCart($this->clientId(), $id);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Batch Delete
     * @desc Batch Delete
     * @author zhaoyj
     * @version v1
     * @url /console/v1/cart/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function batchDelete()
    {
        $ids = $this->request->param('ids', []);
        $this->cartLogic->batchDelete($this->clientId(), $ids);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Clear
     * @desc Clear
     * @author zhaoyj
     * @version v1
     * @url /console/v1/cart/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function clear()
    {
        $this->cartLogic->clear($this->clientId());
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Settle
     * @desc Settle
     * @author zhaoyj
     * @version v1
     * @url /console/v1/cart/:id
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function settle()
    {
        $params = $this->request->param();
        $data = $this->settlementLogic->settle($this->clientId(), $params);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }
}