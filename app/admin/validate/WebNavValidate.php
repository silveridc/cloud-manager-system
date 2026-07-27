<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 网站导航验证器
 * @desc 网站导航验证器
 * @uses \app\admin\validate\WebNavValidate
 */
class WebNavValidate extends Validate
{
    protected $rule = [
        'name' => 'require|length:1,64',
        'url' => 'length:0,255',
        'target' => 'in:_self,_blank',
        'parent_id' => 'integer|egt:0',
        'order' => 'integer|egt:0',
        'status' => 'in:0,1',
    ];

    protected $message = [];

    protected $scene = [
        'create' => ['name', 'url', 'target', 'parent_id', 'order'],
        'update' => ['name', 'url', 'target', 'parent_id', 'order', 'status'],
    ];
}
