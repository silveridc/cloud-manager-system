<?php
namespace app\common\model;

use think\Model;

/**
 * 配置选项模型
 * @desc 配置选项模型
 * @uses \app\common\model\ConfigOptionModel
 */
class ConfigOptionModel extends Model
{
    protected $name = 'config_option';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'         => 'int',
        'product_id' => 'int',
        'name'       => 'string',
        'type'       => 'string',
        'order'      => 'int',
    ];

    // 关联：所属产品

    /**
     * 关联所属产品
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    // 关联：子选项

    /**
     * 关联配置子选项列表
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function subs()
    {
        return $this->hasMany(ConfigOptionSubModel::class, 'config_option_id');
    }
}