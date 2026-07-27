<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 配置验证器
 * @desc 配置验证器
 * @uses \app\admin\validate\ConfigurationValidate
 */
class ConfigurationValidate extends Validate
{
    protected $rule = [
        'setting' => 'require|length:1,128',
        'value' => 'length:0,5000',
    ];

    protected $message = [];

    protected $scene = [
        'update' => ['setting', 'value'],
    ];
}
