<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

/**
 * 任务通知命令
 * @desc 任务通知命令
 * @uses \app\command\TaskNotice
 */
class TaskNotice extends Command
{
    protected function configure(): void
    {
        $this->setName('task_notice')
            ->setDescription('Process notification task queue (email/SMS)');
    }

    /**
     * 执行通知任务队列处理
     * @author zhaoyj
     * @version v1
     * @param Input $input - 输入对象
     * @param Output $output - 输出对象
     * @return int - 退出码
     */
    protected function execute(Input $input, Output $output): int
    {
        $output->writeln('[' . date('Y-m-d H:i:s') . '] Notice processor started');

        $tasks = Db::name('task_notice_wait')
            ->where('status', 'Wait')
            ->order('id', 'asc')
            ->limit(20)
            ->select()->toArray();

        if (empty($tasks)) {
            $output->writeln('  No pending notices');
            return 0;
        }

        foreach ($tasks as $task) {
            $this->processNotice($task, $output);
        }

        $output->writeln('[' . date('Y-m-d H:i:s') . '] Notice processor finished');
        return 0;
    }

    private function processNotice(array $task, Output $output): void
    {
        $output->writeln("  Processing notice #{$task['id']} action={$task['action']}");

        try {
            // TODO: 根据 action 匹配通知设置，发送邮件/短信
            // 需要调用 EmailLogic/SmsLogic 发送

            Db::name('task_notice_wait')->where('id', $task['id'])->update([
                'status' => 'Finish',
                'update_time' => time(),
            ]);
            $output->writeln("    -> Sent");
        } catch (\Throwable $e) {
            Db::name('task_notice_wait')->where('id', $task['id'])->update([
                'status' => 'Failed',
                'error' => $e->getMessage(),
                'update_time' => time(),
            ]);
            $output->writeln("    -> Failed: " . $e->getMessage());
        }
    }
}
