<?php
namespace app\admin\model;

use think\Model;

/**
 * 管理员角色模型
 * @desc 管理员角色模型
 * @uses \app\admin\model\AdminRoleModel
 */
class AdminRoleModel extends Model
{
    protected $name = 'admin_role';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'description' => 'string',
        'level'       => 'int',
        'delegable'   => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    // 关联：角色的管理员

    /**
     * 关联角色下的管理员（多对多）
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsToMany
     */
    public function admins()
    {
        return $this->belongsToMany(AdminModel::class, 'admin_role_link', 'admin_id', 'role_id');
    }
}