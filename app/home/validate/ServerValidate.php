<?php

namespace app\home\validate;

use think\Validate;

/**
 * 服务器验证器
 * @desc 服务器验证器
 * @uses \app\home\validate\ServerValidate
 */
class ServerValidate extends Validate
{
    protected $rule = [
        'name' => 'require|length:1,128',
        'hostname' => 'length:0,255',
        'ip' => 'length:0,64',
        'port' => 'integer|between:1,65535',
        'username' => 'length:0,128',
        'password' => 'length:0,255',
        'server_group_id' => 'integer|egt:0',
        'module' => 'length:0,64',
        'status' => 'in:0,1',
    ];

    protected $message = [];

    protected $scene = [
        'create' => ['name', 'hostname', 'ip', 'port', 'server_group_id', 'module'],
        'update' => ['name', 'hostname', 'ip', 'port', 'server_group_id', 'module', 'status'],
    ];
}
