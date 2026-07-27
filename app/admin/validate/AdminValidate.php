<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 管理员验证器
 * @desc 管理员验证器
 * @uses \app\admin\validate\AdminValidate
 */
class AdminValidate extends Validate
{
    protected $rule = [
        'name' => 'require|length:2,64',
        'email' => 'email',
        'phone' => 'length:5,20',
        'password' => 'require|length:6,64',
        'status' => 'in:0,1',
        'role_ids' => 'array',
    ];

    protected $message = [];

    protected $scene = [
        'create' => ['name', 'password', 'email', 'phone', 'role_ids'],
        'update' => ['name', 'email', 'phone', 'status', 'role_ids'],
    ];
}
