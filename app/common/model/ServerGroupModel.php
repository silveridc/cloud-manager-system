<?php
namespace app\common\model;

use think\Model;

/**
 * 服务器分组模型
 * @desc 服务器分组模型
 * @uses \app\common\model\ServerGroupModel
 */
class ServerGroupModel extends Model
{
    protected $name = 'server_group';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'   => 'int',
        'name' => 'string',
    ];

    // 关联：组内服务器

    /**
     * 组内服务器
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function servers()
    {
        return $this->hasMany(ServerModel::class, 'server_group_id');
    }
}