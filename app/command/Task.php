<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

/**
 * 异步任务命令
 * @desc 异步任务命令
 * @uses \app\command\Task
 */
class Task extends Command
{
    protected function configure(): void
    {
        $this->setName('task')
            ->setDescription('Process task queue (host create/suspend/terminate)');
    }

    /**
     * 执行异步任务队列处理
     * @author zhaoyj
     * @version v1
     * @param Input $input - 输入对象
     * @param Output $output - 输出对象
     * @return int - 退出码
     */
    protected function execute(Input $input, Output $output): int
    {
        $output->writeln('[' . date('Y-m-d H:i:s') . '] Task processor started');

        $tasks = Db::name('task')
            ->where('status', 'Wait')
            ->order('id', 'asc')
            ->limit(10)
            ->select()->toArray();

        if (empty($tasks)) {
            $output->writeln('  No pending tasks');
            return 0;
        }

        foreach ($tasks as $task) {
            $this->processTask($task, $output);
        }

        $output->writeln('[' . date('Y-m-d H:i:s') . '] Task processor finished');
        return 0;
    }

    private function processTask(array $task, Output $output): void
    {
        $output->writeln("  Processing task #{$task['id']} type={$task['type']}");

        Db::name('task')->where('id', $task['id'])->update([
            'status' => 'Exec',
            'update_time' => time(),
        ]);

        try {
            $moduleDispatcher = app()->make(\app\admin\logic\ModuleDispatcher::class);

            $result = match ($task['type']) {
                'create' => $moduleDispatcher->createAccount($task['host_id']),
                'suspend' => $moduleDispatcher->suspendAccount($task['host_id']),
                'unsuspend' => $moduleDispatcher->unsuspendAccount($task['host_id']),
                'terminate' => $moduleDispatcher->terminateAccount($task['host_id']),
                default => ['status' => 400, 'messages' => 'unknown task type'],
            };

            if (($result['status'] ?? 0) == 200) {
                Db::name('task')->where('id', $task['id'])->update([
                    'status' => 'Finish',
                    'update_time' => time(),
                ]);
                $output->writeln("    -> Finished");
            } else {
                Db::name('task')->where('id', $task['id'])->update([
                    'status' => 'Failed',
                    'error' => $result['messages'] ?? 'unknown error',
                    'update_time' => time(),
                ]);
                $output->writeln("    -> Failed: " . ($result['messages'] ?? ''));
            }
        } catch (\Throwable $e) {
            Db::name('task')->where('id', $task['id'])->update([
                'status' => 'Failed',
                'error' => $e->getMessage(),
                'update_time' => time(),
            ]);
            $output->writeln("    -> Exception: " . $e->getMessage());
        }
    }
}
