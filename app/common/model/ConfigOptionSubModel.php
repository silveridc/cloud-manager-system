<?php
namespace app\common\model;

use think\Model;

/**
 * 配置子选项模型
 * @desc 配置子选项模型
 * @uses \app\common\model\ConfigOptionSubModel
 */
class ConfigOptionSubModel extends Model
{
    protected $name = 'config_option_sub';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'               => 'int',
        'config_option_id' => 'int',
        'name'             => 'string',
        'price'            => 'decimal',
        'order'            => 'int',
    ];

    /**
     * 关联所属配置选项
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function configOption()
    {
        return $this->belongsTo(ConfigOptionModel::class, 'config_option_id');
    }
}