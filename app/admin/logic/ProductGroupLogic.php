<?php
namespace app\admin\logic;

use app\common\model\ProductGroupModel;
use app\common\model\ProductModel;

/**
 * 产品分组逻辑
 * @desc 产品分组逻辑
 * @uses \app\admin\logic\ProductGroupLogic
 */
class ProductGroupLogic
{
    /**
     * 获取产品分组列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function GetProductGroupList(array $params): array
    {
        $ProductGroupModel = new ProductGroupModel();
        $ProductModel = new ProductModel();

        $query = $ProductGroupModel;

        if (!empty($params['parent_id']) || (isset($params['parent_id']) && $params['parent_id'] === '0')) {
            $query = $query->where('parent_id', (int)$params['parent_id']);
        }
        if (!empty($params['keywords'])) {
            $query = $query->where('name', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('order', 'asc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 50)
            ->select()->toArray();

        // 批量加载产品数
        if ($list) {
            $groupIds = array_column($list, 'id');
            $productCounts = $ProductModel
                ->whereIn('product_group_id', $groupIds)
                ->field('product_group_id, COUNT(*) as total')
                ->group('product_group_id')
                ->column('total', 'product_group_id');

            foreach ($list as &$item) {
                $item['product_num'] = $productCounts[$item['id']] ?? 0;
            }
        }

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取产品分组树形结构
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function getTree(): array
    {
        $ProductGroupModel = new ProductGroupModel();

        $all = $ProductGroupModel
            ->order('order', 'asc')
            ->select()->toArray();

        return $this->buildTree($all);
    }

    /**
     * 创建产品分组
     * @author zhaoyj
     * @version v1
     * @param array $params - 分组参数
     * @return int
     */
    public function CreateProductGroup(array $params): int
    {
        $ProductGroupModel = new ProductGroupModel();

        return (int)$ProductGroupModel->insertGetId([
            'name' => $params['name'],
            'parent_id' => $params['parent_id'] ?? 0,
            'description' => $params['description'] ?? '',
            'order' => $params['order'] ?? 0,
            'hidden' => $params['hidden'] ?? 0,
            'create_time' => time(),
            'update_time' => time(),
        ]);
    }

    /**
     * 更新产品分组
     * @author zhaoyj
     * @version v1
     * @param int $id - 分组ID
     * @param array $params - 更新参数
     * @return array|void
     */
    public function UpdateProductGroup(int $id, array $params)
    {
        $ProductGroupModel = new ProductGroupModel();

        $group = $ProductGroupModel->where('id', $id)->find();
        if (!$group) {
            return [
                'status' => 404,
                'messages' => lang('product_group_not_found'),
                'time' => time(),
            ];
        }

        $allowFields = ['name', 'parent_id', 'description', 'order', 'hidden'];
        $updateData = ['update_time' => time()];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        $ProductGroupModel->where('id', $id)->update($updateData);
    }

    /**
     * 删除产品分组
     * @author zhaoyj
     * @version v1
     * @param int $id - 分组ID
     * @return array|void
     */
    public function DeleteProductGroup(int $id)
    {
        $ProductGroupModel = new ProductGroupModel();
        $ProductModel = new ProductModel();

        $children = $ProductGroupModel->where('parent_id', $id)->count();
        if ($children > 0) {
            return [
                'status' => 400,
                'messages' => lang('group_has_children'),
                'time' => time(),
            ];
        }

        $productCount = $ProductModel->where('product_group_id', $id)->count();
        if ($productCount > 0) {
            return [
                'status' => 400,
                'messages' => lang('group_has_products'),
                'time' => time(),
            ];
        }

        $ProductGroupModel->where('id', $id)->delete();
    }

    private function buildTree(array $data, int $parentId = 0): array
    {
        $tree = [];
        foreach ($data as $item) {
            if ((int)$item['parent_id'] === $parentId) {
                $item['children'] = $this->buildTree($data, (int)$item['id']);
                $tree[] = $item;
            }
        }
        return $tree;
    }
}