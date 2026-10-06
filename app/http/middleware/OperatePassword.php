<?php

namespace app\http\middleware;

use think\facade\Cache;
use think\facade\Db;
use think\Request;
use think\Response;

/**
 * 操作密码验证中间件
 * @desc 操作密码验证中间件
 * @uses \app\http\middleware\OperatePassword
 */
class OperatePassword
{
    public function handle(Request $request, \Closure $next): Response
    {
        $adminId = $request->adminId ?? 0;
        $clientId = $request->clientId ?? 0;

        $operatePassword = $request->param('operate_password', '');

        // 判断是管理端还是客户端
        if ($adminId) {
            return $this->verifyAdmin($adminId, $this->tokenFingerprint($request), $operatePassword, $request, $next);
        }

        if ($clientId) {
            return $this->verifyClient($clientId, $this->tokenFingerprint($request), $operatePassword, $request, $next);
        }

        return json(['status' => 401, 'messages' => lang('unauthorized'), 'time' => time()], 401);
    }

    private function tokenFingerprint(Request $request): string
    {
        $authorization = $request->header('Authorization', '');
        return hash('sha256', $authorization);
    }

    private function verifyAdmin(int $adminId, string $tokenFingerprint, string $password, Request $request, \Closure $next): Response
    {
        // 15 分钟内已验证过则跳过
        $cacheKey = 'admin_operate_verified:' . $adminId . ':' . $tokenFingerprint;
        if (Cache::get($cacheKey)) {
            return $next($request);
        }

        if (empty($password)) {
            return json([
                'status' => 400,
                'messages' => lang('operate_password_required'),
                'data' => ['operate_password' => 1],
                'time' => time(),
            ], 400);
        }

        $admin = Db::name('admin')->where('id', $adminId)->find();
        if (!$admin || cmf_password($password) !== ($admin['operate_password'] ?? '')) {
            return json([
                'status' => 400,
                'messages' => lang('operate_password_error'),
                'data' => ['operate_password' => 1],
                'time' => time(),
            ], 400);
        }

        // 验证成功，缓存 15 分钟
        Cache::set($cacheKey, 1, 900);

        return $next($request);
    }

    private function verifyClient(int $clientId, string $tokenFingerprint, string $password, Request $request, \Closure $next): Response
    {
        $cacheKey = 'client_operate_verified:' . $clientId . ':' . $tokenFingerprint;
        if (Cache::get($cacheKey)) {
            return $next($request);
        }

        if (empty($password)) {
            return json([
                'status' => 400,
                'messages' => lang('operate_password_required'),
                'data' => ['operate_password' => 1],
                'time' => time(),
            ], 400);
        }

        $client = Db::name('client')->where('id', $clientId)->find();
        if (!$client || cmf_password($password) !== ($client['operate_password'] ?? '')) {
            return json([
                'status' => 400,
                'messages' => lang('operate_password_error'),
                'data' => ['operate_password' => 1],
                'time' => time(),
            ], 400);
        }

        Cache::set($cacheKey, 1, 900);

        return $next($request);
    }
}