<?php
namespace app\common\model;

use think\Model;

/**
 * 产品模型
 * @desc 产品模型
 * @uses \app\common\model\ProductModel
 */
class ProductModel extends Model
{
    protected $name = 'product';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'               => 'int',
        'name'             => 'string',
        'description'      => 'string',
        'type'             => 'string',
        'product_group_id' => 'int',
        'server_group_id'  => 'int',
        'billing_cycle'    => 'string',
        'price'            => 'decimal',
        'stock'            => 'int',
        'hidden'           => 'int',
        'order'            => 'int',
        'create_time'      => 'int',
        'update_time'      => 'int',
    ];

    // 关联：所属产品组

    /**
     * 关联所属产品组
     * @author zhaoyj
     * @version v1
     * @param mixed $group
     * @return \think\model\relation\BelongsTo
     */
    public function group($group)
    {
        return $this->belongsTo(ProductGroupModel::class, 'product_group_id');
    }

    // 关联：产品的配置选项

    /**
     * 关联产品配置选项
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function configOptions()
    {
        return $this->hasMany(ConfigOptionModel::class, 'product_id');
    }

    // 关联：关联的产品实例

    /**
     * 关联产品实例
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function hosts()
    {
        return $this->hasMany(HostModel::class, 'product_id');
    }

    // 关联：关联的服务器组

    /**
     * 关联服务器组
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function serverGroup()
    {
        return $this->belongsTo(ServerGroupModel::class, 'server_group_id');
    }

    // 作用域：上架中

    /**
     * 作用域：上架中
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeOnSale($query)
    {
        return $query->where('hidden', 0);
    }

    // 作用域：下架

    /**
     * 作用域：已下架
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeHidden($query)
    {
        return $query->where('hidden', 1);
    }

    // 获取器：格式化价格

    /**
     * 获取器：格式化产品价格
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始价格值
     * @return string - 格式化后的价格字符串
     */
    public function getPriceAttr($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }
}