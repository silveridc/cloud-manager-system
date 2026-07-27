<?php
namespace app\common\model;

use think\Model;

/**
 * 系统配置模型
 * @desc 系统配置模型
 * @uses \app\common\model\ConfigurationModel
 */
class ConfigurationModel extends Model
{
    protected $name = 'configuration';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'      => 'int',
        'setting' => 'string',
        'value'   => 'string',
        'group'   => 'string',
    ];
}