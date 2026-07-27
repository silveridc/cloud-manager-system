<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'cron' => \app\command\Cron::class,
        'task' => \app\command\Task::class,
        'task_notice' => \app\command\TaskNotice::class,
    ],
];
