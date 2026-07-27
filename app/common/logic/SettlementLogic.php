<?php
namespace app\common\logic;

use app\common\enum\HostStatus;
use app\common\enum\OrderStatus;
use app\common\model\OrderModel;
use app\common\model\ProductModel;
use app\common\model\HostModel;
use app\common\model\OrderItemModel;
use app\common\model\CartModel;
use think\facade\Event;

/**
 * 结算逻辑
 * @uses \app\common\logic\SettlementLogic
 */
class SettlementLogic
{
    /**
     * 结算（从购物车或直接购买）
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params - 结算参数
     * @return array
     * @throws \Throwable
     */
    public function settle(int $clientId, array $params): array
    {
        $items = $this->resolveItems($clientId, $params);

        if (empty($items)) {
            return [
                'status' => 400,
                'messages' => lang('cart_empty'),
                'time' => time(),
            ];
        }

        $OrderModel = new OrderModel();
        $OrderModel->startTrans();
        try {
            $ProductModel = new ProductModel();
            $HostModel = new HostModel();
            $OrderItemModel = new OrderItemModel();
            $CartModel = new CartModel();

            $totalAmount = 0;
            $orderItems = [];
            $hostIds = [];

            // 创建订单
            $orderId = (int)$OrderModel->insertGetId([
                'client_id' => $clientId,
                'type' => 'new',
                'status' => OrderStatus::Unpaid->value,
                'amount' => 0, // 先占位，后面更新
                'create_time' => time(),
                'update_time' => time(),
            ]);

            foreach ($items as $item) {
                $product = (new ProductModel())->where('id', $item['product_id'])->find();
                if (!$product) {
                    return [
                        'status' => 404,
                        'messages' => lang('product_not_found'),
                        'time' => time(),
                    ];
                }

                // 原子扣减库存，WHERE 条件防止超卖
                if ($product['stock'] >= 0) {
                    $affected = (new ProductModel())
                        ->where('id', $product['id'])
                        ->where('stock', '>=', $item['qty'])
                        ->dec('stock', $item['qty'])
                        ->update();

                    if ($affected <= 0) {
                        $OrderModel->rollback();
                        return [
                            'status' => 400,
                            'messages' => lang('insufficient_stock') ?: '库存不足',
                            'time' => time(),
                        ];
                    }
                }

                $price = (float)$product['price'] * $item['qty'];
                $totalAmount += $price;

                // 创建产品实例
                $hostId = (int)(new HostModel())->insertGetId([
                    'client_id' => $clientId,
                    'product_id' => $product['id'],
                    'server_id' => 0,
                    'name' => $product['name'],
                    'status' => HostStatus::Unpaid->value,
                    'billing_cycle' => $item['billing_cycle'] ?? $product['billing_cycle'],
                    'first_payment_amount' => $price,
                    'renew_amount' => $price,
                    'create_time' => time(),
                    'update_time' => time(),
                ]);

                $hostIds[] = $hostId;

                // 创建订单明细
                $orderItems[] = [
                    'order_id' => $orderId,
                    'product_id' => $product['id'],
                    'host_id' => $hostId,
                    'type' => 'host',
                    'amount' => $price,
                    'description' => $product['name'],
                ];
            }

            // 写入订单明细
            $OrderItemModel->insertAll($orderItems);

            // 更新订单总金额
            (new OrderModel())->where('id', $orderId)->update([
                'amount' => $totalAmount,
                'host_id' => $hostIds[0] ?? 0,
            ]);

            // 清除对应购物车
            if (!empty($params['cart_ids'])) {
                $CartModel
                    ->whereIn('id', $params['cart_ids'])
                    ->where('client_id', $clientId)
                    ->delete();
            }

            Event::trigger('after_order_create', [
                'order_id' => $orderId,
                'client_id' => $clientId,
                'host_ids' => $hostIds,
            ]);

            $OrderModel->commit();

            return [
                'order_id' => $orderId,
                'amount' => amount_format($totalAmount),
            ];
        } catch (\Throwable $e) {
            $OrderModel->rollback();
            throw $e;
        }
    }

    /**
     * 解析结算项目
     */
    private function resolveItems(int $clientId, array $params): array
    {
        // 从购物车结算
        if (!empty($params['cart_ids'])) {
            $CartModel = new CartModel();

            return $CartModel
                ->whereIn('id', $params['cart_ids'])
                ->where('client_id', $clientId)
                ->field('product_id, qty, config_options, billing_cycle')
                ->select()->toArray();
        }

        // 直接购买
        if (!empty($params['product_id'])) {
            return [[
                'product_id' => $params['product_id'],
                'qty' => $params['qty'] ?? 1,
                'config_options' => $params['config_options'] ?? '',
                'billing_cycle' => $params['billing_cycle'] ?? '',
            ]];
        }

        return [];
    }
}