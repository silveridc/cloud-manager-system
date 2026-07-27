<?php
namespace app\admin\model;

use think\Model;

/**
 * 客户操作记录模型
 * @desc 客户操作记录模型
 * @uses \app\admin\model\ClientRecordModel
 */
class ClientRecordModel extends Model
{
    protected $name = 'client_record';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'client_id'   => 'int',
        'admin_id'    => 'int',
        'content'     => 'string',
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
}