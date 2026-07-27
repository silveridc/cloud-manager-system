<?php
namespace app\admin\logic;

use app\admin\model\SupplierModel;
use app\common\model\UpstreamProductModel;
use think\facade\Event;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 供应商逻辑
 * @desc 供应商逻辑
 * @uses \app\admin\logic\SupplierLogic
 */
class SupplierLogic
{
    /**
     * 获取供应商列表
     * @author zhaoyj
     * @version v1
     * @param array $params ['limit'] - 每页条数，默认20
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function GetSupplierList(array $params): array
    {
        $SupplierModel = new SupplierModel();

        $query = $SupplierModel
            ->field(['id', 'name', 'url', 'username', 'credit', 'status', 'create_time', 'update_time']);

        if (!empty($params['keywords'])) {
            $query->where('name|url', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取供应商详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 供应商ID
     * @return array|null
     * @return int id - 供应商ID
     * @return string name - 供应商名称
     * @return string url - 供应商URL
     * @return string username - 供应商用户名
     * @return string token - 供应商Token（脱敏处理，仅显示后4位）
     * @return float credit - 供应商余额
     * @return int status - 状态
     * @return int create_time - 创建时间
     * @return int update_time - 更新时间
     */
    public function GetSupplierInfo(int $id): ?array
    {
        $SupplierModel = new SupplierModel();

        $supplier = $SupplierModel->where('id', $id)->find();
        if (!$supplier) {
            return null;
        }

        $supplier = $supplier->toArray();

        // Mask token: only show last 4 characters to prevent information disclosure
        if (!empty($supplier['token'])) {
            $supplier['token'] = str_repeat('*', max(0, mb_strlen($supplier['token']) - 4))
                . mb_substr($supplier['token'], -4);
        }

        return $supplier;
    }

    /**
     * 创建供应商
     * @author zhaoyj
     * @version v1
     * @param array $params ['status'] - 状态，默认1（启用）
     * @return int 新创建的供应商ID
     */
    public function CreateSupplier(array $params): int
    {
        $SupplierModel = new SupplierModel();

        $id = (int)$SupplierModel->insertGetId([
            'name' => $params['name'],
            'url' => $params['url'] ?? '',
            'username' => $params['username'] ?? '',
            'token' => $params['token'] ?? '',
            'credit' => 0,
            'status' => $params['status'] ?? 1,
            'create_time' => time(),
            'update_time' => time(),
        ]);

        Event::trigger('after_supplier_create', ['supplier_id' => $id]);
        return $id;
    }

    /**
     * 更新供应商信息
     * @author zhaoyj
     * @version v1
     * @param int $id - 供应商ID
     * @param array $params ['status'] - 状态
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function UpdateSupplier(int $id, array $params)
    {
        $SupplierModel = new SupplierModel();

        $supplier = $SupplierModel->where('id', $id)->find();
        if (!$supplier) {
            return [
                'status' => 404,
                'messages' => lang('supplier_not_found'),
                'time' => time(),
            ];
        }

        $allowFields = ['name', 'url', 'username', 'token', 'status'];
        $updateData = ['update_time' => time()];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        $SupplierModel->where('id', $id)->update($updateData);
    }

    /**
     * 删除供应商
     * @author zhaoyj
     * @version v1
     * @param int $id - 供应商ID
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function DeleteSupplier(int $id)
    {
        $SupplierModel = new SupplierModel();

        $supplier = $SupplierModel->where('id', $id)->find();
        if (!$supplier) {
            return [
                'status' => 404,
                'messages' => lang('supplier_not_found'),
                'time' => time(),
            ];
        }

        // 检查是否有关联的上游产品
        $UpstreamProductModel = new UpstreamProductModel();
        $productCount = $UpstreamProductModel->where('supplier_id', $id)->count();
        if ($productCount > 0) {
            return [
                'status' => 400,
                'messages' => lang('supplier_has_products'),
                'time' => time(),
            ];
        }

        $SupplierModel->where('id', $id)->delete();
    }
}