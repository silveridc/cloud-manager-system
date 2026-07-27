<?php

namespace app\home\validate;

use think\Validate;

/**
 * 认证验证器
 * @desc 认证验证器
 * @uses \app\home\validate\AuthValidate
 */
class AuthValidate extends Validate
{
    protected $rule = [
        'username' => 'require|length:2,64',
        'password' => 'require|length:6,64',
        'account' => 'require|length:2,128',
        'email' => 'email',
        'phone' => 'mobile',
        'old_password' => 'require|length:6,64',
        'new_password' => 'require|length:6,64|different:old_password',
        'remember_password' => 'boolean',
    ];

    protected $message = [];

    protected $scene = [
        'admin_login' => ['username', 'password'],
        'client_login' => ['account', 'password'],
        'register' => ['password', 'email'],
        'change_password' => ['old_password', 'new_password'],
    ];
}
