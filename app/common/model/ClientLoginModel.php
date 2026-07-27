<?php
namespace app\common\model;

use think\Model;

/**
 * 客户登录记录模型
 * @desc 客户登录记录模型
 * @uses \app\common\model\ClientLoginModel
 */
class ClientLoginModel extends Model
{
    protected $name = 'client_login';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'client_id'   => 'int',
        'ip'          => 'string',
        'create_time' => 'int',
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
}