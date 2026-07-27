<?php
namespace app\common\model;

use think\Model;

/**
 * 订单模型
 * @desc 订单模型
 * @uses \app\common\model\OrderModel
 */
class OrderModel extends Model
{
    protected $name = 'order';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'            => 'int',
        'client_id'     => 'int',
        'host_id'       => 'int',
        'type'          => 'string',
        'status'        => 'string',
        'amount'        => 'decimal',
        'credit_amount' => 'decimal',
        'gateway'       => 'string',
        'pay_time'      => 'int',
        'create_time'   => 'int',
        'update_time'   => 'int',
    ];

    // 关联：订单所属客户

    /**
     * 关联订单所属客户
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    // 关联：订单明细

    /**
     * 关联订单明细
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id');
    }

    // 关联：订单关联的产品实例

    /**
     * 关联订单的产品实例
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo(HostModel::class, 'host_id');
    }

    // 关联：交易记录

    /**
     * 关联交易记录
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(TransactionModel::class, 'order_id');
    }

    // 作用域：未支付

    /**
     * 作用域：未支付订单
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'Unpaid');
    }

    // 作用域：已支付

    /**
     * 作用域：已支付订单
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    // 获取器：金额格式化

    /**
     * 获取器：格式化订单金额
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始金额值
     * @return string - 格式化后的金额字符串
     */
    public function getAmountAttr($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }
}