<?php
namespace app\common\entity;

use think\Entity;
use app\common\model\TransactionModel;
/**
 * 交易记录实体
 * @desc 交易记录实体
 * @uses \app\common\entity\TransactionEntity
 */
class TransactionEntity extends Entity
{
    protected function getOptions(): array
    {
        return [
            'modelClass' => TransactionModel::class,
        ];
    }
    /**
     * @title 管理端流水列表
     * @desc 管理端流水列表
     * @author zhaoyj
     * @version v1
     */
    public function listForAdmin(array $params): array
    {
        $query = $this->model()->db()
            ->alias('t')
            ->leftJoin('client c', 'c.id = t.client_id')
            ->leftJoin('order o', 'o.id = t.order_id')
            ->field([
                't.id', 't.client_id', 't.order_id', 't.amount',
                't.gateway', 't.transaction_id', 't.create_time',
                'c.username as client_name', 'c.email as client_email',
                'o.type as order_type', 'o.status as order_status',
            ]);

        if (!empty($params['client_id'])) {
            $query->where('t.client_id', (int)$params['client_id']);
        }
        if (!empty($params['gateway'])) {
            $query->where('t.gateway', $params['gateway']);
        }
        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->whereOr('t.transaction_id', 'like', $kw)
                    ->whereOr('c.username', 'like', $kw)
                    ->whereOr('c.email', 'like', $kw);
            });
        }
        if (!empty($params['start_time'])) {
            $query->where('t.create_time', '>=', strtotime($params['start_time']));
        }
        if (!empty($params['end_time'])) {
            $query->where('t.create_time', '<=', strtotime($params['end_time']));
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('t.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * @title 客户端流水列表
     * @desc 客户端流水列表
     * @author zhaoyj
     * @version v1
     */
    public function listForClient(int $clientId, array $params): array
    {
        $query = $this->model()->db()
            ->alias('t')
            ->leftJoin('order o', 'o.id = t.order_id')
            ->field([
                't.id', 't.order_id', 't.amount', 't.gateway',
                't.transaction_id', 't.create_time',
                'o.type as order_type',
            ])
            ->where('t.client_id', $clientId);

        if (!empty($params['gateway'])) {
            $query->where('t.gateway', $params['gateway']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('t.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }
}