<?php
namespace app\common\model;

use think\Model;

/**
 * 任务通知等待模型
 * @desc 任务通知等待模型
 * @uses \app\common\model\TaskNoticeWaitModel
 */
class TaskNoticeWaitModel extends Model
{
    protected $name = 'task_notice_wait';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'             => 'int',
        'action'         => 'string',
        'client_id'      => 'int',
        'host_id'        => 'int',
        'order_id'       => 'int',
        'template_param' => 'string',
        'status'         => 'string',
        'error'          => 'string',
        'create_time'    => 'int',
        'update_time'    => 'int',
    ];
}