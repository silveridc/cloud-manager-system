<?php
namespace app\admin\model;

use think\Model;

/**
 * 侧边浮窗模型
 * @desc 侧边浮窗模型
 * @uses \app\admin\model\SideFloatingWindowModel
 */
class SideFloatingWindowModel extends Model
{
    protected $name = 'side_floating_window';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'       => 'int',
        'name'     => 'string',
        'url'      => 'string',
        'icon'     => 'string',
        'image'    => 'string',
        'content'  => 'string',
        'order'    => 'int',
        'status'   => 'int',
        'position' => 'string',
    ];
}