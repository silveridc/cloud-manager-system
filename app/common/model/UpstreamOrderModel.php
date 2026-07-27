<?php
namespace app\common\model;
use app\admin\model\SupplierModel;
use think\Model;
use think\db\Raw;
/**
 * 上游订单模型
 * @desc 上游订单模型
 * @uses \app\common\model\UpstreamOrderModel
 */
class UpstreamOrderModel extends Model
{
    protected $name = 'upstream_order';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'supplier_id' => 'int',
        'order_id'    => 'int',
        'create_time' => 'int',
    ];

    /**
     * 关联所属供应商
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id');
    }

    /**
     * 关联所属订单
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function order(string|array|Raw $field, string $order = '')
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }
}