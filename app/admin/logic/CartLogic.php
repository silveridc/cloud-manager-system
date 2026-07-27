<?php
namespace app\admin\logic;

use app\common\model\CartModel;
use app\common\model\ProductModel;

/**
 * 购物车逻辑
 * @desc 购物车逻辑
 * @uses \app\admin\logic\CartLogic
 */
class CartLogic
{
    /**
     * 获取购物车列表
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户id
     * @return array - 购物车列表
     */
    public function getList(int $clientId): array
    {
        $CartModel = new CartModel();

        $list = $CartModel
            ->alias('c')
            ->leftJoin('product p', 'p.id = c.product_id')
            ->field([
                'c.id', 'c.product_id', 'c.qty', 'c.config_options',
                'c.billing_cycle', 'c.position', 'c.create_time',
                'p.name as product_name', 'p.type as product_type',
            ])
            ->where('c.client_id', $clientId)
            ->order('c.position', 'asc')
            ->select()->toArray();

        foreach ($list as &$item) {
            $item['config_options'] = $item['config_options'] ? json_decode($item['config_options'], true) : [];
        }

        return $list;
    }

    /**
     * 创建购物车项
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户id
     * @param array $params - 购物车参数
     * @return int - 购物车项id
     */
    public function create(int $clientId, array $params): int
    {
        $CartModel = new CartModel();
        $ProductModel = new ProductModel();

        // 验证产品存在且上架
        $product = $ProductModel
            ->where('id', $params['product_id'])
            ->where('hidden', 0)
            ->find();

        if (!$product) {
            return [
                'status' => 404,
                'messages' => lang('product_not_found'),
                'time' => time(),
            ];
        }

        // 检查库存
        if ($product['stock'] >= 0 && $product['stock'] < ($params['qty'] ?? 1)) {
            return [
                'status' => 400,
                'messages' => lang('product_out_of_stock'),
                'time' => time(),
            ];
        }

        $maxPosition = $CartModel
            ->where('client_id', $clientId)
            ->max('position') ?? 0;

        $CartModel = new CartModel();
        $id = (int)$CartModel->insertGetId([
            'client_id' => $clientId,
            'product_id' => $params['product_id'],
            'qty' => $params['qty'] ?? 1,
            'config_options' => !empty($params['config_options']) ? json_encode($params['config_options'], JSON_UNESCAPED_UNICODE) : '',
            'billing_cycle' => $params['billing_cycle'] ?? $product['billing_cycle'],
            'position' => $maxPosition + 1,
            'create_time' => time(),
        ]);

        return $id;
    }

    /**
     * 更新购物车项
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户id
     * @param int $id - 购物车项id
     * @param array $params - 更新参数
     * @return void
     */
    public function update(int $clientId, int $id, array $params)
    {
        $CartModel = new CartModel();

        $cart = $CartModel
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->find();

        if (!$cart) {
            return [
                'status' => 404,
                'messages' => lang('cart_item_not_found'),
                'time' => time(),
            ];
        }

        $updateData = [];
        if (isset($params['qty'])) {
            $updateData['qty'] = max(1, (int)$params['qty']);
        }
        if (isset($params['config_options'])) {
            $updateData['config_options'] = json_encode($params['config_options'], JSON_UNESCAPED_UNICODE);
        }
        if (isset($params['billing_cycle'])) {
            $updateData['billing_cycle'] = $params['billing_cycle'];
        }

        if ($updateData) {
            $CartModel = new CartModel();
            $CartModel->where('id', $id)->update($updateData);
        }
    }

    /**
     * 删除购物车项
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户id
     * @param int $id - 购物车项id
     * @return void
     */
    public function delete(int $clientId, int $id): void
    {
        $CartModel = new CartModel();

        $CartModel
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->delete();
    }

    /**
     * 批量删除购物车项
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户id
     * @param array $ids - 购物车项id数组
     * @return void
     */
    public function batchDelete(int $clientId, array $ids): void
    {
        $CartModel = new CartModel();

        $CartModel
            ->whereIn('id', $ids)
            ->where('client_id', $clientId)
            ->delete();
    }

    /**
     * 清空购物车
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户id
     * @return void
     */
    public function clear(int $clientId): void
    {
        $CartModel = new CartModel();

        $CartModel
            ->where('client_id', $clientId)
            ->delete();
    }
}