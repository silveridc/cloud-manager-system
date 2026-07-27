<?php
namespace app\admin\controller;

use app\admin\logic\ProductLogic;
use app\admin\validate\ProductValidate;

/**
 * 管理端产品控制器
 * @desc 管理端产品控制器
 * @uses \app\admin\controller\ProductController
 */
class ProductController extends AdminBaseController
{
    protected ProductLogic $productLogic;
    protected ProductValidate $ProductValidate;
    protected function initialize()
    {
        parent::initialize();
        $this->productLogic = app(ProductLogic::class);
        $this->ProductValidate = app(ProductValidate::class);
    }

    /**
     * @title 获取产品列表
     * @desc 获取产品列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return array data.list - 产品列表
     * @return int data.count - 总数
     * @return int time - 时间戳
     */
    public function GetProductList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->productLogic->GetProductList($params);
        if (isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
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
     * @url /admin/v1/product/:id
     * @method get
     * @param int id - 产品ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return object data - 产品详情
     * @return int time - 时间戳
     */
    public function GetProductInfo(int $id)
    {
        $data = $this->productLogic->GetProductInfo($id);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
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

    /**
     * @title 创建产品
     * @desc 创建产品
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product
     * @method post
     * @param string name - 产品名称 required
     * @param string type - 产品类型
     * @param int product_group_id - 产品组ID
     * @param string billing_cycle - 计费周期,free,onetime,recurring_prepayment,recurring_postpaid,on_demand
     * @param float price - 价格
     * @param int stock - 库存
     * @param int hidden - 是否隐藏(0否1是)
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int data.id - 产品ID
     * @return int time - 时间戳
     */
    public function CreateProduct()
    {
        $params = $this->request->param();
        if(!$this->ProductValidate->scene('create')->check($params)){
            return json(['status' => 403 , 'messages' => lang($this->ProductValidate->getError())],403);
        }
        $id = $this->productLogic->CreateProduct($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $id,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 更新产品
     * @desc 更新产品
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product/:id
     * @method put
     * @param int id - 产品ID required
     * @param string name - 产品名称
     * @param string type - 产品类型
     * @param int product_group_id - 产品组ID
     * @param string billing_cycle - 计费周期
     * @param float price - 价格
     * @param int stock - 库存
     * @param int hidden - 是否隐藏(0否1是)
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateProduct(int $id)
    {
        $params = $this->request->param();
        if(!$this->ProductValidate->scene('update')->check($params)){
            return json(['status' => 403 , 'messages' => lang($this->ProductValidate->getError())],403);
        }
        $result = $this->productLogic->UpdateProduct($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除产品
     * @desc 删除产品
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product/:id
     * @method delete
     * @param int id - 产品ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteProduct(int $id)
    {
        $result = $this->productLogic->DeleteProduct($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}
