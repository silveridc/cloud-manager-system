<?php
namespace app\common\model;

use think\Model;

/**
 * 购物车模型
 * @desc 购物车模型
 * @uses \app\common\model\CartModel
 */
class CartModel extends Model
{
    protected $name = 'cart';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'             => 'int',
        'client_id'      => 'int',
        'product_id'     => 'int',
        'qty'            => 'int',
        'config_options' => 'string',
        'billing_cycle'  => 'string',
        'position'       => 'int',
        'create_time'    => 'int',
    ];

    /**
     * 关联所属客户
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    /**
     * 关联所属商品
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}