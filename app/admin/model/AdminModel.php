<?php
namespace app\admin\model;

use think\Model;

/**
 * 管理员模型
 * @desc 管理员模型
 * @uses \app\admin\model\AdminModel
 */
class AdminModel extends Model
{
    protected $name = 'admin';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 隐藏敏感字段
    protected $hidden = ['password', 'operate_password'];

    protected $schema = [
        'id'               => 'int',
        'name'             => 'string',
        'email'            => 'string',
        'phone'            => 'string',
        'password'         => 'string',
        'operate_password' => 'string',
        'status'           => 'int',
        'session_version'  => 'int',
        'last_login_time'  => 'int',
        'last_login_ip'    => 'string',
        'last_action_time' => 'int',
        'create_time'      => 'int',
        'update_time'      => 'int',
    ];

    // 关联：角色

    /**
     * 关联角色
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(AdminRoleModel::class, 'admin_role_link', 'role_id', 'admin_id');
    }

    // 作用域：启用

    /**
     * 作用域：启用
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}