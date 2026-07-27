<?php
namespace app\admin\controller;

use app\admin\logic\SupplierLogic;

/**
 * 供应商控制器
 * @desc 供应商控制器
 * @uses \app\admin\controller\SupplierController
 */
class SupplierController extends AdminBaseController
{
    protected SupplierLogic $supplierLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->supplierLogic = app(SupplierLogic::class);
    }

    /**
     * @title 获取供应商列表
     * @desc 获取供应商列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/supplier
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetSupplierList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->supplierLogic->GetSupplierList($params);
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
     * @title 获取供应商详情
     * @desc 获取供应商详情
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/supplier/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetSupplierInfo(int $id)
    {
        $data = $this->supplierLogic->GetSupplierInfo($id);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('supplier_not_found'),
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
     * @title 创建供应商
     * @desc 创建供应商
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/supplier
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CreateSupplier()
    {
        $params = $this->request->param();
        $id = $this->supplierLogic->CreateSupplier($params);
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
     * @title 修改供应商
     * @desc 修改供应商
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/supplier/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateSupplier(int $id)
    {
        $params = $this->request->param();
        $result = $this->supplierLogic->UpdateSupplier($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除供应商
     * @desc 删除供应商
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/supplier/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteSupplier(int $id)
    {
        $result = $this->supplierLogic->DeleteSupplier($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}