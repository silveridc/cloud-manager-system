<?php

namespace app\http\middleware;

use think\facade\Cache;
use think\facade\Db;
use think\Request;
use think\Response;

/**
 * 维护模式中间件
 * @desc 维护模式中间件
 * @uses \app\http\middleware\MaintenanceMode
 */
class MaintenanceMode
{
    public function handle(Request $request, \Closure $next): Response
    {
        $maintenance = Cache::remember('system:maintenance_mode', function () {
            $val = Db::name('configuration')->where('setting', 'maintenance_mode')->value('value');
            return $val === '1';
        }, 60);

        if ($maintenance) {
            return json([
                'status' => 503,
                'messages' => lang('maintenance_mode'),
                'time' => time(),
            ], 503);
        }

        return $next($request);
    }
}