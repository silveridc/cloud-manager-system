<?php

namespace app\home\logic;

use app\common\enum\OrderStatus;
use think\facade\Event;
use app\common\entity\OrderEntity;
use app\common\service\OrderLifecycleService;
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
    protected OrderLifecycleService $lifecycleService;

    public function __construct(OrderEntity $entity, OrderModel $model, OrderLifecycleService $lifecycleService)
    {
        $this->entity = $entity;
        $this->model = $model;
        $this->lifecycleService = $lifecycleService;
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
        return $this->lifecycleService->cancelUnpaidOrder($id, $clientId);
    }
}