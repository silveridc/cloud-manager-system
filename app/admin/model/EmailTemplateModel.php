<?php
namespace app\admin\model;

use think\Model;

/**
 * 邮件模板模型
 * @desc 邮件模板模型
 * @uses \app\admin\model\EmailTemplateModel
 */
class EmailTemplateModel extends Model
{
    protected $name = 'email_template';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'      => 'int',
        'name'    => 'string',
        'subject' => 'string',
        'content' => 'string',
        'status'  => 'int',
    ];
}