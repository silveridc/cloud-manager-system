<?php
namespace app\admin\logic;

use app\common\model\ClientCreditModel;
use app\common\model\ClientModel;
use think\facade\Event;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 客户余额逻辑
 * @desc 客户余额逻辑
 * @uses \app\admin\logic\ClientCreditLogic
 */
class ClientCreditLogic
{
    /**
     * 获取客户余额变动记录列表
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params .sort - 排序方式（默认desc）
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getRecords(int $clientId, array $params): array
    {
        $ClientCreditModel = new ClientCreditModel();
        $query = $ClientCreditModel
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

    /**
     * 管理端充值或扣费操作
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param float $amount - 变动金额（正数为充值，负数为扣费）
     * @param string $type - 变动类型
     * @param string $notes - 备注说明（默认为空）
     * @return array|void
     * @throws \Throwable 数据库事务异常时抛出
     */
    public function updateCredit(int $clientId, float $amount, string $type, string $notes = '')
    {
        $client = ClientModel::find($clientId);
        if (!$client) {
            return [
                'status' => 404,
                'messages' => lang('client_not_found'),
                'time' => time(),
            ];
        }

        $client->startTrans();
        try {
            // 更新余额
            $newCredit = (float)$client->getAttr('credit') + $amount;
            $client->save(['credit' => $newCredit]);

            // 记录余额变动
            $record = new ClientCreditModel();
            $record->save([
                'client_id' => $clientId,
                'type' => $type,
                'amount' => $amount,
                'balance' => $newCredit,
                'notes' => $notes,
            ]);

            Event::trigger('after_client_credit_update', [
                'client_id' => $clientId,
                'amount' => $amount,
                'type' => $type,
            ]);

            $client->commit();
        } catch (\Throwable $e) {
            $client->rollback();
            throw $e;
        }
    }
}