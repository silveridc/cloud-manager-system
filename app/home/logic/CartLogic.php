<?php

namespace app\home\logic;

use app\common\model\CartModel;
use app\common\model\ProductModel;

/**
 * 购物车逻辑
 * @desc 购物车逻辑
 * @uses \app\home\logic\CartLogic
 */
class CartLogic
{
    /**
     * 获取购物车列表
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @return array
     * @return array id - 购物车项ID
     * @return array product_id - 产品ID
     * @return array qty - 数量
     * @return array config_options - 配置选项(已解码为数组)
     * @return array billing_cycle - 计费周期
     * @return array position - 排序位置
     * @return array create_time - 创建时间
     * @return array product_name - 产品名称
     * @return array product_type - 产品类型
     */
    public function GetCartList(int $clientId): array
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
     * 添加商品到购物车
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params - 购物车参数
     * @param array params.product_id - 产品ID
     * @param array params.qty - 数量(可选，默认1)
     * @param array params.config_options - 配置选项(可选)
     * @param array params.billing_cycle - 计费周期(可选，默认取产品计费周期)
     * @return int|array 新建购物车项ID或错误数组
     */
    public function AddToCart(int $clientId, array $params): int|array
    {
        $ProductModel = new ProductModel();
        $CartModel = new CartModel();

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

        $cartItem = new CartModel();
        $cartItem->save([
            'client_id' => $clientId,
            'product_id' => $params['product_id'],
            'qty' => $params['qty'] ?? 1,
            'config_options' => !empty($params['config_options']) ? json_encode($params['config_options'], JSON_UNESCAPED_UNICODE) : '',
            'billing_cycle' => $params['billing_cycle'] ?? $product['billing_cycle'],
            'position' => $maxPosition + 1,
            'create_time' => time(),
        ]);

        $id = (int)$cartItem->getAttr('id');

        return $id;
    }

    /**
     * 更新购物车项
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param int $id - 购物车项ID
     * @param array $params - 更新参数
     * @param array params.qty - 数量(可选)
     * @param array params.config_options - 配置选项(可选)
     * @param array params.billing_cycle - 计费周期(可选)
     * @return array|void 错误数组或无返回
     */
    public function UpdateCart(int $clientId, int $id, array $params)
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
            $cart->save($updateData);
        }
    }

    /**
     * 删除购物车项
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param int $id - 购物车项ID
     * @return void
     */
    public function DeleteCart(int $clientId, int $id): void
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
     * @param int $clientId - 客户ID
     * @param array $ids - 购物车项ID数组
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
     * @param int $clientId - 客户ID
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