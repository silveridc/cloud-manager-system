<?php
namespace app\common\logic;

use app\common\model\UpstreamHostModel;
use app\common\model\UpstreamOrderModel;
use app\common\model\UpstreamProductModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 上游逻辑
 * @desc 上游逻辑
 * @uses \app\common\logic\UpstreamLogic
 */
class UpstreamLogic
{
    /**
     * 获取上游产品列表
     * @author zhaoyj
     * @version v1
     * @param array $params ['limit'] - 每页条数，默认20
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getProductList(array $params): array
    {
        $UpstreamProductModel = new UpstreamProductModel();
        $query = $UpstreamProductModel->alias('up')
            ->leftJoin('supplier s', 's.id = up.supplier_id')
            ->leftJoin('product p', 'p.id = up.product_id')
            ->field([
                'up.*',
                's.name as supplier_name',
                'p.name as local_product_name',
            ]);

        if (!empty($params['supplier_id'])) {
            $query->where('up.supplier_id', (int)$params['supplier_id']);
        }
        if (!empty($params['keywords'])) {
            $query->where('up.name|s.name', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('up.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取上游订单列表
     * @author zhaoyj
     * @version v1
     * @param array $params ['limit'] - 每页条数，默认20
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getOrderList(array $params): array
    {
        $UpstreamOrderModel = new UpstreamOrderModel();
        $query = $UpstreamOrderModel->alias('uo')
            ->leftJoin('supplier s', 's.id = uo.supplier_id')
            ->leftJoin('order o', 'o.id = uo.order_id')
            ->field([
                'uo.*',
                's.name as supplier_name',
                'o.amount as local_amount',
            ]);

        if (!empty($params['supplier_id'])) {
            $query->where('uo.supplier_id', (int)$params['supplier_id']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('uo.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取上游主机列表
     * @author zhaoyj
     * @version v1
     * @param array $params ['limit'] - 每页条数，默认20
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getHostList(array $params): array
    {
        $UpstreamHostModel = new UpstreamHostModel();
        $query = $UpstreamHostModel->alias('uh')
            ->leftJoin('supplier s', 's.id = uh.supplier_id')
            ->leftJoin('host h', 'h.id = uh.host_id')
            ->field([
                'uh.*',
                's.name as supplier_name',
                'h.name as local_host_name',
                'h.status as local_host_status',
            ]);

        if (!empty($params['supplier_id'])) {
            $query->where('uh.supplier_id', (int)$params['supplier_id']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('uh.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }
}