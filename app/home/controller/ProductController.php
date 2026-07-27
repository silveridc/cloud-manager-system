<?php

namespace app\home\controller;

use app\home\logic\ProductLogic;

/**
 * 产品控制器
 * @desc 产品控制器
 * @uses \app\home\controller\ProductController
 */
class ProductController extends HomeBaseController
{
    protected ProductLogic $productLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->productLogic = app(ProductLogic::class);
    }

    /**
     * @title 获取产品列表
     * @desc 获取产品列表
     * @author zhaoyj
     * @version v1
     * @url /console/v1/product
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetProductList()
    {
        $params = array_merge($this->request->param(), $this->paginationParams());
        $data = $this->productLogic->getClientList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取产品详情
     * @desc 获取产品详情
     * @author zhaoyj
     * @version v1
     * @url /console/v1/product/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetProductInfo(int $id)
    {
        $data = $this->productLogic->getDetail($id);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('product_not_found'),
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
}