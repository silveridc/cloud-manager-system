<?php
namespace app\admin\model;

use think\Model;

/**
 * 插件钩子模型
 * @desc 插件钩子模型
 * @uses \app\admin\model\PluginHookModel
 */
class PluginHookModel extends Model
{
    protected $name = 'plugin_hook';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'        => 'int',
        'plugin_id' => 'int',
        'hook'      => 'string',
        'class'     => 'string',
        'priority'  => 'int',
    ];
}