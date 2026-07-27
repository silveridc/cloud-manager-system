<?php

namespace app\home\logic;

use app\common\enum\OrderStatus;
use think\facade\Event;
use app\common\entity\OrderEntity;
use app\common\model\OrderModel;

/**
 * 客户端订单业务逻辑
 * @desc 客户端订单业务逻辑
 * @uses \app\home\logic\OrderLogic
 */
class OrderLogic
{
    protected OrderEntity $entity;
    protected OrderModel $model;

    public function __construct(OrderEntity $entity, OrderModel $model)
    {
        $this->entity = $entity;
        $this->model = $model;
    }

    /**
     * 客户端订单列表
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params - 查询参数
     * @return array
     */
    public function getClientList(int $clientId, array $params): array
    {
        return $this->entity->listForClient($clientId, $params);
    }

    /**
     * 订单详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 订单ID
     * @param int|null $clientId - 客户ID（可选，用于权限校验）
     * @return array|null
     */
    public function GetOrderInfo(int $id, ?int $clientId = null): ?array
    {
        return $this->entity->detail($id, $clientId);
    }

    /**
     * 取消订单
     * @author zhaoyj
     * @version v1
     * @param int $id - 订单ID
     * @param int|null $clientId - 客户ID（可选，用于权限校验）
     * @return array|null
     */
    public function CancelOrder(int $id, ?int $clientId = null)
    {
        $order = $this->model->find($id);
        if (!$order) {
            return [
                'status' => 404,
                'messages' => lang('order_not_found'),
                'time' => time(),
            ];
        }

        if ($clientId !== null && $order->getAttr('client_id') != $clientId) {
            return [
                'status' => 404,
                'messages' => lang('order_not_found'),
                'time' => time(),
            ];
        }

        if ($order->getAttr('status') !== OrderStatus::Unpaid->value) {
            return [
                'status' => 400,
                'messages' => lang('order_cannot_cancel'),
                'time' => time(),
            ];
        }

        $order->startTrans();
        try {
            $order->save([
                'status' => OrderStatus::Cancelled->value,
            ]);

            Event::trigger('after_order_cancel', ['order_id' => $id]);

            $order->commit();
        } catch (\Throwable $e) {
            $order->rollback();
            throw $e;
        }
    }
}