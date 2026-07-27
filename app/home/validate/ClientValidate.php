<?php

namespace app\home\validate;

use think\Validate;

/**
 * 客户验证器
 * @desc 客户验证器
 * @uses \app\home\validate\ClientValidate
 */
class ClientValidate extends Validate
{
    protected $rule = [
        'username' => 'require|length:2,64',
        'email' => 'email',
        'phone' => 'length:5,20',
        'phone_code' => 'length:1,10',
        'password' => 'require|length:6,64',
        'company' => 'length:0,255',
        'address' => 'length:0,512',
        'language' => 'in:zh-cn,en-us',
        'country_id' => 'integer|egt:0',
        'status' => 'in:0,1',
        'notes' => 'length:0,1000',
    ];

    protected $message = [];

    protected $scene = [
        'create' => ['username', 'password', 'email', 'phone', 'company', 'address', 'language', 'country_id'],
        'update' => ['username', 'email', 'phone', 'company', 'address', 'language', 'country_id', 'status', 'notes'],
        'client_update' => ['email', 'phone', 'company', 'address', 'language'],
    ];
}
