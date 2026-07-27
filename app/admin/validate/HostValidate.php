<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 主机验证器
 * @desc 主机验证器
 * @uses \app\admin\validate\HostValidate
 */
class HostValidate extends Validate
{
    protected $rule = [
        'notes' => 'length:0,500',
        'reason' => 'length:0,500',
        'auto_release_time' => 'integer|egt:0',
    ];

    protected $message = [];

    protected $scene = [
        'update_notes' => ['notes'],
        'suspend' => ['reason'],
        'auto_release' => ['auto_release_time'],
    ];
}
