<?php
namespace app\admin\model;

use think\Model;

/**
 * 系统日志模型
 * @desc 系统日志模型
 * @uses \app\admin\model\SystemLogModel
 */
class SystemLogModel extends Model
{
    protected $name = 'system_log';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'description' => 'string',
        'type'        => 'string',
        'rel_id'      => 'int',
        'admin_id'    => 'int',
        'client_id'   => 'int',
        'ip'          => 'string',
        'create_time' => 'int',
    ];
}