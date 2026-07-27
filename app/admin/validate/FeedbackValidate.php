<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 反馈验证器
 * @desc 反馈验证器
 * @uses \app\admin\validate\FeedbackValidate
 */
class FeedbackValidate extends Validate
{
    protected $rule = [
        'content' => 'require|length:1,5000',
        'title' => 'length:0,255',
        'type_id' => 'integer|egt:0',
    ];

    protected $message = [];

    protected $scene = [
        'create' => ['content', 'title', 'type_id'],
    ];
}
