<?php
namespace app\common\model;

use think\Model;

/**
 * 任务模型
 * @desc 任务模型
 * @uses \app\common\model\TaskModel
 */
class TaskModel extends Model
{
    protected $name = 'task';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'          => 'int',
        'type'        => 'string',
        'description' => 'string',
        'host_id'     => 'int',
        'client_id'   => 'int',
        'status'      => 'string',
        'error'       => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    // 关联：所属产品实例

    /**
     * 关联所属产品实例
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo(HostModel::class, 'host_id');
    }

    // 作用域：待执行

    /**
     * 作用域：筛选待执行的任务
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'Wait');
    }

    // 作用域：失败

    /**
     * 作用域：筛选执行失败的任务
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'Failed');
    }
}