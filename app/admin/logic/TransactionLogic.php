<?php
namespace app\admin\logic;

use app\common\model\TransactionModel;

/**
 * 交易记录逻辑
 * @desc 交易记录逻辑
 * @uses \app\admin\logic\TransactionLogic
 */
class TransactionLogic
{
    /**
     * 获取交易记录列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @param int params.client_id - 客户id
     * @param string params.gateway - 支付网关
     * @param string params.keywords - 搜索关键字
     * @param string params.start_time - 开始时间
     * @param string params.end_time - 结束时间
     * @param string params.sort - 排序规则
     * @param int params.page - 页码
     * @param int params.limit - 每页条数
     * @return array
     * @return array list - 交易列表
     * @return int list.id - 交易id
     * @return int list.client_id - 客户id
     * @return int list.order_id - 订单id
     * @return float list.amount - 交易金额
     * @return string list.gateway - 支付网关
     * @return string list.transaction_id - 第三方交易号
     * @return int list.create_time - 创建时间
     * @return string list.client_name - 客户名称
     * @return int count - 总数量
     */
    public function GetTransactionList(array $params): array
    {
        $TransactionModel = new TransactionModel();
        $query = $TransactionModel->alias('t')
            ->leftJoin('client c', 'c.id = t.client_id')
            ->leftJoin('order o', 'o.id = t.order_id')
            ->field([
                't.id', 't.client_id', 't.order_id', 't.amount',
                't.gateway', 't.transaction_id', 't.create_time',
                'c.username as client_name',
            ]);

        if (!empty($params['client_id'])) {
            $query->where('t.client_id', (int)$params['client_id']);
        }
        if (!empty($params['gateway'])) {
            $query->where('t.gateway', $params['gateway']);
        }
        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where('t.transaction_id|c.username', 'like', $kw);
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
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }
}