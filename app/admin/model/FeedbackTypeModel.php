<?php
namespace app\admin\model;

use think\Model;

/**
 * 反馈类型模型
 * @desc 反馈类型模型
 * @uses \app\admin\model\FeedbackTypeModel
 */
class FeedbackTypeModel extends Model
{
    protected $name = 'feedback_type';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'    => 'int',
        'name'  => 'string',
        'order' => 'int',
    ];
}