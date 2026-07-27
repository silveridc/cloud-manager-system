<?php
namespace app\admin\model;
use app\common\model\OrderModel;
use think\Model;
use think\db\Raw;
/**
 * 退款记录模型
 * @desc 退款记录模型
 * @uses \app\admin\model\RefundRecordModel
 */
class RefundRecordModel extends Model
{
    protected $name = 'refund_record';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'            => 'int',
        'order_id'      => 'int',
        'client_id'     => 'int',
        'admin_id'      => 'int',
        'amount'        => 'decimal',
        'reason'        => 'string',
        'status'        => 'string',
        'reject_reason' => 'string',
        'create_time'   => 'int',
        'update_time'   => 'int',
    ];

    /**
     * 关联客户
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    /**
     * 关联订单
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function order(string|array|Raw $field, string $order = '')
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }
}