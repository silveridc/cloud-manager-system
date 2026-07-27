<?php
namespace app\admin\model;

use think\Model;

/**
 * 自定义主机名模型
 * @desc 自定义主机名模型
 * @uses \app\admin\model\CustomHostNameModel
 */
class CustomHostNameModel extends Model
{
    protected $name = 'custom_host_name';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'    => 'int',
        'name'  => 'string',
        'order' => 'int',
    ];
}