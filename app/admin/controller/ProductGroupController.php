<?php
namespace app\admin\controller;

use app\admin\logic\ProductGroupLogic;

/**
 * 产品分组控制器
 * @desc 产品分组控制器
 * @uses \app\admin\controller\ProductGroupController
 */
class ProductGroupController extends AdminBaseController
{
    protected ProductGroupLogic $productGroupLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->productGroupLogic = app(ProductGroupLogic::class);
    }

    /**
     * @title 获取产品分组列表
     * @desc 获取产品分组列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product_group
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetProductGroupList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->productGroupLogic->GetProductGroupList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Tree
     * @desc Tree
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product_group/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function tree()
    {
        $data = $this->productGroupLogic->getTree();
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 创建产品分组
     * @desc 创建产品分组
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product_group
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CreateProductGroup()
    {
        $params = $this->request->param();
        $id = $this->productGroupLogic->CreateProductGroup($params);
        if (is_array($id) && isset($id['status'])) return json($id, $id['status']);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $id,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 修改产品分组
     * @desc 修改产品分组
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product_group/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateProductGroup(int $id)
    {
        $params = $this->request->param();
        $result = $this->productGroupLogic->UpdateProductGroup($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除产品分组
     * @desc 删除产品分组
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/product_group/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteProductGroup(int $id)
    {
        $result = $this->productGroupLogic->DeleteProductGroup($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}