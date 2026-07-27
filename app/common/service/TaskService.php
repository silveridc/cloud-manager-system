<?php

namespace app\common\service;
use app\common\model\TaskModel;
use app\common\model\TaskNoticeWaitModel;

/**
 * 任务服务
 * @desc 任务服务
 * @uses \app\common\service\TaskService
 */
class TaskService
{
    /**
     * 添加异步任务
     */
    public static function add(array $params): int
    {
        $TaskModel = new TaskModel();
        $data = [
            'type' => $params['type'] ?? '',
            'description' => $params['description'] ?? '',
            'host_id' => $params['host_id'] ?? 0,
            'client_id' => $params['client_id'] ?? 0,
            'status' => 'Wait',
            'create_time' => time(),
        ];

        return (int)$TaskModel->insertGetId($data);
    }

    /**
     * 发送系统通知
     */
    public static function notice(array $params): void
    {
        $TaskNoticeWaitModel = new TaskNoticeWaitModel();
        $TaskNoticeWaitModel->insert([
            'action' => $params['action'] ?? '',
            'client_id' => $params['client_id'] ?? 0,
            'host_id' => $params['host_id'] ?? 0,
            'order_id' => $params['order_id'] ?? 0,
            'template_param' => json($params['template_param'] ?? [])->getContent(),
            'status' => 'Wait',
            'create_time' => time(),
        ]);
    }
}