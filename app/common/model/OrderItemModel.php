<?php
namespace app\common\model;

use think\Model;
use think\db\Raw;
use think\model\relation\BelongsTo;

/**
 * 订单项模型
 * @desc 订单项模型
 * @uses \app\common\model\OrderItemModel
 */
class OrderItemModel extends Model
{
    protected $name = 'order_item';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'          => 'int',
        'order_id'    => 'int',
        'product_id'  => 'int',
        'host_id'     => 'int',
        'type'        => 'string',
        'qty'         => 'int',
        'stock_reserved' => 'int',
        'amount'      => 'decimal',
        'description' => 'string',
    ];

    /**
     * 关联所属订单
     * @author zhaoyj
     * @version v1
     * @param string|array|Raw $field
     * @param string $order * @return \think\model\relation\BelongsTo
     * @return BelongsTo
     */
    public function order(string|array|Raw $field, string $order = '')
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }

    /**
     * 关联所属产品
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    /**
     * 关联产品实例（主机）
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo(HostModel::class, 'host_id');
    }
}