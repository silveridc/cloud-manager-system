<?php

namespace app\http\middleware;

use app\common\service\JwtService;
use think\facade\Cache;
use think\Request;
use think\Response;

/**
 * 可选认证中间件
 * @desc 可选认证中间件
 * @uses \app\http\middleware\OptionalAuth
 */
class OptionalAuth
{
    public function handle(Request $request, \Closure $next): Response
    {
        $token = $this->extractToken($request);
        if (!$token) {
            return $next($request);
        }

        // 验证 token 有效性
        if (!Cache::get('client_token:' . sha1($token))) {
            return $next($request);
        }

        $payload = JwtService::decodeClient($token);
        if ($payload) {
            $request->clientId = (int)$payload['id'];
            $request->clientName = $payload['name'] ?? '';
        }

        return $next($request);
    }

    private function extractToken(Request $request): string
    {
        $auth = $request->header('Authorization', '');
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return '';
    }
}