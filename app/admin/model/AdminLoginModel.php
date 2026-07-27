<?php
namespace app\admin\model;

use think\Model;

/**
 * 管理员登录记录模型
 * @desc 管理员登录记录模型
 * @uses \app\admin\model\AdminLoginModel
 */
class AdminLoginModel extends Model
{
    protected $name = 'admin_login';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'admin_id'    => 'int',
        'ip'          => 'string',
        'create_time' => 'int',
    ];
}