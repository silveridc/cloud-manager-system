<?php

namespace app\home\logic;

use app\common\entity\HostEntity;
use app\common\model\HostModel;

/**
 * 客户端产品实例业务逻辑
 * @desc 客户端产品实例业务逻辑
 * @uses \app\home\logic\HostLogic
 */
class HostLogic
{
    protected HostEntity $entity;
    protected HostModel $model;

    public function __construct(HostEntity $entity, HostModel $model)
    {
        $this->entity = $entity;
        $this->model = $model;
    }

    /**
     * 获取客户产品实例列表
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params - 查询参数(分页、筛选等)
     * @return array 产品实例列表及分页信息
     */
    public function getClientList(int $clientId, array $params): array
    {
        return $this->entity->listForClient($clientId, $params);
    }

    /**
     * 获取产品实例详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品实例ID
     * @param int|null $clientId - 客户ID(传入时验证归属权限)
     * @return array|null 产品实例详情，不存在时返回null
     */
    public function GetHostInfo(int $id, ?int $clientId = null): ?array
    {
        return $this->entity->detail($id, $clientId);
    }

    /**
     * 更新产品实例备注
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品实例ID
     * @param string $notes - 备注内容
     * @param int|null $clientId - 客户ID(传入时验证归属权限)
     * @return array|null 错误时返回错误数组
     */
    public function UpdateHostNotes(int $id, string $notes, ?int $clientId = null)
    {
        $query = $this->model->where('id', $id);
        if ($clientId !== null) {
            $query->where('client_id', $clientId);
        }

        $host = $query->find();
        if (!$host) {
            return [
                'status' => 404,
                'messages' => lang('host_not_found'),
                'time' => time(),
            ];
        }

        $host->save(['notes' => $notes]);
    }
}