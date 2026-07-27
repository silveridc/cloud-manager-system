<?php
namespace app\admin\model;

use think\Model;

/**
 * 管理员权限规则模型
 * @desc 管理员权限规则模型
 * @uses \app\admin\model\AdminRuleModel
 */
class AdminRuleModel extends Model
{
    protected $name = 'admin_rule';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'      => 'int',
        'role_id' => 'int',
        'name'    => 'string',
    ];
}