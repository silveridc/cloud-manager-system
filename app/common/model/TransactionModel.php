<?php
namespace app\common\model;

use think\Model;
use think\db\Raw;
/**
 * 交易记录模型
 * @desc 交易记录模型
 * @uses \app\common\model\TransactionModel
 */
class TransactionModel extends Model
{
    protected $name = 'transaction';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'             => 'int',
        'client_id'      => 'int',
        'order_id'       => 'int',
        'amount'         => 'decimal',
        'gateway'        => 'string',
        'transaction_id' => 'string',
        'create_time'    => 'int',
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

    // 关联：所属订单

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

    // 获取器

    /**
     * 获取器：格式化交易金额为两位小数
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始值
     * @return string - 格式化后的金额字符串
     */
    public function getAmountAttr($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }
}