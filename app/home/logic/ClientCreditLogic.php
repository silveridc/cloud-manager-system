<?php

namespace app\home\logic;

use app\common\model\ClientCreditModel;

/**
 * 客户端余额逻辑
 * @desc 客户端余额逻辑
 * @uses \app\home\logic\ClientCreditLogic
 */
class ClientCreditLogic
{
    protected ClientCreditModel $creditModel;

    public function __construct(ClientCreditModel $creditModel)
    {
        $this->creditModel = $creditModel;
    }

    /**
     * 获取余额变动记录
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params - 查询参数
     * @param array params.type - 变动类型(可选，用于筛选)
     * @param array params.sort - 排序方式(可选，默认desc)
     * @param array params.page - 页码(可选，默认1)
     * @param array params.limit - 每页条数(可选，默认20)
     * @return array
     * @return array list - 余额变动记录列表
     * @return array count - 总记录数
     */
    public function getRecords(int $clientId, array $params): array
    {
        $query = $this->creditModel
            ->where('client_id', $clientId);

        if (!empty($params['type'])) {
            $query->where('type', $params['type']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }
}