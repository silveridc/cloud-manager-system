<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * 定时任务命令
 * @desc 定时任务命令
 * @uses \app\command\Cron
 */
class Cron extends Command
{
    protected function configure(): void
    {
        $this->setName('cron')
            ->setDescription('Main scheduled task runner');
    }

    /**
     * 执行定时任务
     * @author zhaoyj
     * @version v1
     * @param Input $input - 输入对象
     * @param Output $output - 输出对象
     * @return int - 退出码
     */
    protected function execute(Input $input, Output $output): int
    {
        $output->writeln('[' . date('Y-m-d H:i:s') . '] Cron started');

        // 每分钟任务
        $this->minuteTasks($output);

        $output->writeln('[' . date('Y-m-d H:i:s') . '] Cron finished');
        return 0;
    }

    private function minuteTasks(Output $output): void
    {
        // TODO: 清理过期缓存
        // TODO: 删除超时未支付订单
        // TODO: 触发 minute_cron hook
        $output->writeln('  - Minute tasks done');
    }
}
