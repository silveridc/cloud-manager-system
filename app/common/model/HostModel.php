<?php
namespace app\common\model;

use think\Model;

/**
 * 主机实例模型
 * @desc 主机实例模型
 * @uses \app\common\model\HostModel
 */
class HostModel extends Model
{
    protected $name = 'host';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'                   => 'int',
        'client_id'            => 'int',
        'product_id'           => 'int',
        'server_id'            => 'int',
        'name'                 => 'string',
        'status'               => 'string',
        'billing_cycle'        => 'string',
        'first_payment_amount' => 'decimal',
        'renew_amount'         => 'decimal',
        'notes'                => 'string',
        'suspend_reason'       => 'string',
        'suspend_time'         => 'int',
        'terminate_time'       => 'int',
        'active_time'          => 'int',
        'due_time'             => 'int',
        'create_time'          => 'int',
        'update_time'          => 'int',
    ];

    // 关联：所属客户

    /**
     * 关联所属客户
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    // 关联：关联的产品

    /**
     * 关联产品
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    // 关联：关联的服务器

    /**
     * 关联服务器
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function server()
    {
        return $this->belongsTo(ServerModel::class, 'server_id');
    }

    // 关联：IP 地址

    /**
     * 关联IP地址
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function ips()
    {
        return $this->hasMany(HostIpModel::class, 'host_id');
    }

    // 关联：订单明细

    /**
     * 关联订单明细
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItemModel::class, 'host_id');
    }

    // 作用域：活跃状态

    /**
     * 作用域：活跃状态
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // 作用域：未删除

    /**
     * 作用域：未删除
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('status', '<>', 'Deleted');
    }

    // 作用域：指定客户

    /**
     * 作用域：指定客户
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @param int $clientId - 客户id
     * @return \think\db\Query
     */
    public function scopeOfClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // 获取器：金额格式化

    /**
     * 获取器：格式化首次付款金额
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始金额值
     * @return string - 格式化后的金额字符串
     */
    public function getFirstPaymentAmountAttr($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }

    /**
     * 获取器：格式化续费金额
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始金额值
     * @return string - 格式化后的金额字符串
     */
    public function getRenewAmountAttr($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }
}