<?php

namespace app\common\service;

use app\common\enum\HostStatus;
use app\common\model\HostModel;
use app\common\model\OrderItemModel;
use app\common\model\OrderModel;
use app\common\model\ProductModel;
use think\facade\Event;

/**
 * 未支付订单的取消和库存释放。
 */
class OrderLifecycleService
{
    /**
     * 取消未支付订单并释放本次结算预留的库存。
     *
     * @param int $orderId
     * @param int|null $clientId 传入时验证订单归属
     * @return array|null
     * @throws \Throwable
     */
    public function cancelUnpaidOrder(int $orderId, ?int $clientId = null): ?array
    {
        $OrderModel = new OrderModel();
        $OrderModel->startTrans();

        try {
            $query = $OrderModel->where('id', $orderId);
            if ($clientId !== null) {
                $query->where('client_id', $clientId);
            }
            $order = $query->lock(true)->find();

            if (!$order) {
                $OrderModel->rollback();
                return [
                    'status' => 404,
                    'messages' => lang('order_not_found'),
                    'time' => time(),
                ];
            }

            if ($order->getAttr('status') !== OrderStatus::Unpaid->value) {
                $OrderModel->rollback();
                return [
                    'status' => 400,
                    'messages' => lang('order_cannot_cancel'),
                    'time' => time(),
                ];
            }

            $order->save([
                'status' => OrderStatus::Cancelled->value,
            ]);

            $items = (new OrderItemModel())
                ->where('order_id', $orderId)
                ->where('stock_reserved', 1)
                ->lock(true)
                ->select();

            foreach ($items as $item) {
                (new ProductModel())
                    ->where('id', $item->getAttr('product_id'))
                    ->where('stock', '>=', 0)
                    ->inc('stock', $item->getAttr('qty'))
                    ->update();

                $item->save(['stock_reserved' => 0]);
            }

            $hostIds = (new OrderItemModel())->where('order_id', $orderId)->column('host_id');
            if (!empty($hostIds)) {
                (new HostModel())
                    ->whereIn('id', $hostIds)
                    ->where('status', HostStatus::Unpaid->value)
                    ->update(['status' => HostStatus::Cancelled->value]);
            }

            Event::trigger('after_order_cancel', ['order_id' => $orderId]);
            $OrderModel->commit();
            return null;
        } catch (\Throwable $e) {
            $OrderModel->rollback();
            throw $e;
        }
    }
}
