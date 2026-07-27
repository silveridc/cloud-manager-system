<?php

namespace app\home\logic;

use app\common\entity\ProductEntity;

/**
 * 客户端产品业务逻辑
 * @desc 客户端产品业务逻辑
 * @uses \app\home\logic\ProductLogic
 */
class ProductLogic
{
    protected ProductEntity $entity;

    public function __construct(ProductEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * 客户端产品列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function getClientList(array $params): array
    {
        return $this->entity->listForClient($params);
    }

    /**
     * 产品详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品ID
     * @return array|null
     */
    public function getDetail(int $id): ?array
    {
        return $this->entity->detail($id);
    }
}