<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 产品验证器
 * @desc 产品验证器
 * @uses \app\admin\validate\ProductValidate
 */
class ProductValidate extends Validate
{
    protected $rule = [
        'name' => 'require|length:1,255',
        'description' => 'length:0,10000',
        'type' => 'length:0,64',
        'product_group_id' => 'integer|egt:0',
        'server_group_id' => 'integer|egt:0',
        'billing_cycle' => 'in:free,onetime,recurring_prepayment,recurring_postpaid,on_demand',
        'price' => 'float|egt:0',
        'stock' => 'integer|egt:-1',
        'hidden' => 'in:0,1',
        'order' => 'integer|egt:0',
    ];

    protected $message = [];

    protected $scene = [
        'create' => ['name', 'type', 'product_group_id', 'billing_cycle', 'price', 'stock', 'hidden'],
        'update' => ['name', 'description', 'type', 'product_group_id', 'server_group_id', 'billing_cycle', 'price', 'stock', 'hidden', 'order'],
    ];
}
