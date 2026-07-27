<?php

namespace app\common\enum;

/**
 * 任务状态
 */
enum TaskStatus: string
{
    case Wait = 'Wait';
    case Exec = 'Exec';
    case Finish = 'Finish';
    case Failed = 'Failed';

    /**
     * 获取状态标签
     * @author zhaoyj
     * @version v1
     * @return string - 多语言标签
     */
    public function label(): string
    {
        return match ($this) {
            self::Wait => lang('task_status_wait'),
            self::Exec => lang('task_status_exec'),
            self::Finish => lang('task_status_finish'),
            self::Failed => lang('task_status_failed'),
        };
    }
}