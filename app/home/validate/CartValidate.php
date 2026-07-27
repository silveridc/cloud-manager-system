<?php

namespace app\home\validate;

use think\Validate;

/**
 * 购物车验证器
 * @desc 购物车验证器
 * @uses \app\home\validate\CartValidate
 */
class CartValidate extends Validate
{
    protected $rule = [
        'product_id' => 'require|integer|gt:0',
        'qty' => 'integer|egt:1',
        'billing_cycle' => 'in:free,onetime,recurring_prepayment,recurring_postpaid,on_demand',
        'config_options' => 'array',
        'cart_ids' => 'require|array',
        'ids' => 'require|array',
    ];

    protected $message = [];

    protected $scene = [
        'create' => ['product_id', 'qty', 'billing_cycle'],
        'update' => ['qty', 'billing_cycle', 'config_options'],
        'settle' => [],
        'batch_delete' => ['ids'],
    ];
}
