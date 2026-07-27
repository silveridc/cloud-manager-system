<?php

namespace app\http\middleware;

use think\facade\Cache;
use think\Request;
use think\Response;

/**
 * 防重复提交中间件
 * @desc 防重复提交中间件
 * @uses \app\http\middleware\ThrottleRepeat
 */
class ThrottleRepeat
{
    public function handle(Request $request, \Closure $next): Response
    {
        // 只对写操作做防重
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return $next($request);
        }

        $ip = $request->ip();
        $url = $request->url();
        $params = $request->param();
        unset($params['is_api']);

        $cacheKey = 'throttle_repeat:' . sha1($ip . $url . serialize($params));

        if (Cache::get($cacheKey)) {
            return json([
                'status' => 400,
                'messages' => lang('repeat_request'),
                'time' => time(),
            ], 400);
        }

        Cache::set($cacheKey, 1, 3);

        return $next($request);
    }
}