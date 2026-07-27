<?php
namespace app\common\model;

use think\Model;

/**
 * 产品分组模型
 * @desc 产品分组模型
 * @uses \app\common\model\ProductGroupModel
 */
class ProductGroupModel extends Model
{
    protected $name = 'product_group';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'parent_id'   => 'int',
        'description' => 'string',
        'order'       => 'int',
        'hidden'      => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    // 关联：下属产品

    /**
     * 下属产品
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'product_group_id');
    }

    // 关联：上级分组

    /**
     * 上级分组
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // 关联：子分组

    /**
     * 子分组
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}