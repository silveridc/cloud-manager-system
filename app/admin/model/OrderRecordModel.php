<?php
namespace app\admin\model;
use app\common\model\OrderModel;
use think\Model;
use think\db\Raw;
/**
 * 订单操作记录模型
 * @desc 订单操作记录模型
 * @uses \app\admin\model\OrderRecordModel
 */
class OrderRecordModel extends Model
{
    protected $name = 'order_record';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'order_id'    => 'int',
        'admin_id'    => 'int',
        'content'     => 'string',
        'create_time' => 'int',
    ];

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