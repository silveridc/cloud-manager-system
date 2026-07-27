<?php
namespace app\common\model;

use think\Model;

/**
 * 管理员小组件模型
 * @desc 管理员小组件模型
 * @uses \app\common\model\AdminWidgetModel
 */
class AdminWidgetModel extends Model
{
    protected $name = 'admin_widget';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'       => 'int',
        'admin_id' => 'int',
        'widget'   => 'string',
        'order'    => 'int',
    ];
}