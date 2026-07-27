<?php
namespace app\event;
use think\facade\Event;
/**
 * AppInit事件类
 */
class AppInit
{
    /**
     * handle类，可以添加其他内容
     */
    public function Handle(Event $event): void
    {
        if(app()->isDebug()){
            $env = 'development';
        } else {
            $env = 'production';
        }
        // 初始化 Sentry
        \Sentry\init([
            'release'=> config('app.sentry.release','CloudManagerSystem@v1.0.0'),
            'dsn' => 'https://316a44e2a36152bd24d4326df6f40690@sentry.silveridc.cn/4',
            'environment' => $env,
        ]);
    }
}