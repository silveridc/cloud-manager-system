<?php
namespace app\admin\model;

use think\Model;

/**
 * 反馈模型
 * @desc 反馈模型
 * @uses \app\admin\model\FeedbackModel
 */
class FeedbackModel extends Model
{
    protected $name = 'feedback';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'title'       => 'string',
        'content'     => 'string',
        'type_id'     => 'int',
        'client_id'   => 'int',
        'status'      => 'string',
        'create_time' => 'int',
    ];

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

    /**
     * 关联反馈类型
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(FeedbackTypeModel::class, 'type_id');
    }
}