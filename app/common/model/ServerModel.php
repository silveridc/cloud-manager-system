<?php
namespace app\common\model;

use think\Model;

/**
 * 服务器模型
 * @desc 服务器模型
 * @uses \app\common\model\ServerModel
 */
class ServerModel extends Model
{
    protected $name = 'server';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'              => 'int',
        'name'            => 'string',
        'hostname'        => 'string',
        'ip'              => 'string',
        'port'            => 'int',
        'username'        => 'string',
        'password'        => 'string',
        'server_group_id' => 'int',
        'module'          => 'string',
        'status'          => 'int',
        'create_time'     => 'int',
        'update_time'     => 'int',
    ];

    // 关联：所属服务器组

    /**
     * 所属服务器组
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function group($group)
    {
        return $this->belongsTo(ServerGroupModel::class, 'server_group_id');
    }

    // 关联：下挂的产品实例

    /**
     * 下挂的产品实例
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function hosts()
    {
        return $this->hasMany(HostModel::class, 'server_id');
    }

    // 作用域：启用

    /**
     * 作用域：启用
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }
}