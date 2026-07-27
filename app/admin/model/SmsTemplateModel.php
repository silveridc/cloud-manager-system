<?php
namespace app\admin\model;

use think\Model;

/**
 * 短信模板模型
 * @desc 短信模板模型
 * @uses \app\admin\model\SmsTemplateModel
 */
class SmsTemplateModel extends Model
{
    protected $name = 'sms_template';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'      => 'int',
        'name'    => 'string',
        'content' => 'string',
        'status'  => 'int',
    ];
}