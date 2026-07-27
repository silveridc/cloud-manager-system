<?php
namespace app\admin\model;

use think\Model;

/**
 * 管理员角色关联模型
 * @desc 管理员角色关联模型
 * @uses \app\admin\model\AdminRoleLinkModel
 */
class AdminRoleLinkModel extends Model
{
    protected $name = 'admin_role_link';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'       => 'int',
        'admin_id' => 'int',
        'role_id'  => 'int',
    ];
}