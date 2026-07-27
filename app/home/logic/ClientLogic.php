<?php

namespace app\home\logic;

use app\home\entity\ClientEntity;
use app\common\model\ClientModel;

/**
 * 客户端客户业务逻辑
 * @desc 客户端客户业务逻辑
 * @uses \app\home\logic\ClientLogic
 */
class ClientLogic
{
    protected ClientEntity $entity;
    protected ClientModel $model;

    public function __construct(ClientEntity $entity, ClientModel $model)
    {
        $this->entity = $entity;
        $this->model = $model;
    }

    /**
     * 获取客户个人资料
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @return array|null 客户资料信息，不存在时返回null
     */
    public function getProfile(int $clientId): ?array
    {
        return $this->entity->profile($clientId);
    }

    /**
     * 更新客户个人资料
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params - 更新参数
     * @param array params.username - 用户名(可选)
     * @param array params.email - 邮箱(可选)
     * @param array params.phone_code - 手机区号(可选)
     * @param array params.phone - 手机号(可选)
     * @param array params.company - 公司名称(可选)
     * @param array params.address - 地址(可选)
     * @param array params.language - 语言(可选)
     * @param array params.country_id - 国家ID(可选)
     * @return array|void 错误时返回错误数组
     */
    public function updateProfile(int $clientId, array $params)
    {
        $client = $this->model->find($clientId);
        if (!$client) {
            return [
                'status' => 404,
                'messages' => lang('client_not_found'),
                'time' => time(),
            ];
        }

        $allowFields = ['username', 'email', 'phone_code', 'phone', 'company', 'address', 'language', 'country_id'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if (!empty($updateData)) {
            $client->save($updateData);
        }
    }
}