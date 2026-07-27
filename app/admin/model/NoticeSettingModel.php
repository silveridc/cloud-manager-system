<?php
namespace app\admin\model;

use think\Model;

/**
 * 通知设置模型
 * @desc 通知设置模型
 * @uses \app\admin\model\NoticeSettingModel
 */
class NoticeSettingModel extends Model
{
    protected $name = 'notice_setting';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'            => 'int',
        'sms_global'    => 'int',
        'sms_account'   => 'int',
        'email_global'  => 'int',
        'email_account' => 'int',
    ];
}