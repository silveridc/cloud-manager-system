<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 订单验证器
 * @desc 订单验证器
 * @uses \app\admin\validate\OrderValidate
 */
class OrderValidate extends Validate
{
    protected $rule = [
        'order_id' => 'require|integer|gt:0',
        'gateway' => 'require|length:1,64',
        'amount' => 'require|float|gt:0',
        'reason' => 'length:0,500',
        'content' => 'require|length:1,2000',
    ];

    protected $message = [];

    protected $scene = [
        'pay' => ['order_id', 'gateway'],
        'refund' => ['amount', 'reason'],
        'record' => ['content'],
    ];
}
