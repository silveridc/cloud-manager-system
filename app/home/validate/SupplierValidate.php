<?php

namespace app\home\validate;

use think\Validate;

/**
 * 供应商验证器
 * @desc 供应商验证器
 * @uses \app\home\validate\SupplierValidate
 */
class SupplierValidate extends Validate
{
    protected $rule = [
        'name' => 'require|length:1,128',
        'url' => 'url',
        'username' => 'length:0,128',
        'token' => 'length:0,512',
        'status' => 'in:0,1',
    ];

    protected $message = [];

    protected $scene = [
        'create' => ['name', 'url'],
        'update' => ['name', 'url', 'username', 'token', 'status'],
    ];
}
