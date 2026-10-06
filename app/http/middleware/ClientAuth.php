<?php

namespace app\http\middleware;

use app\common\service\JwtService;
use think\facade\Cache;
use think\facade\Db;
use think\Request;
use think\Response;

/**
 * 客户认证中间件
 * @desc 客户认证中间件
 * @uses \app\http\middleware\ClientAuth
 */
class ClientAuth
{
    /**
     * @title 客户认证验证
     * @desc 客户认证验证
     * @author zhaoyj
     * @version v1
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $token = $this->extractToken($request);
        if (!$token) {
            return json(['status' => 401, 'messages' => lang('unauthorized'), 'time' => time()], 401);
        }

        // 验证 token 是否已注销
        if (!Cache::get('client_token:' . sha1($token))) {
            return json(['status' => 401, 'messages' => lang('token_expired'), 'time' => time()], 401);
        }

        // 解码 JWT
        $payload = JwtService::decodeClient($token);
        if (!$payload) {
            return json(['status' => 401, 'messages' => lang('token_invalid'), 'time' => time()], 401);
        }

        // 检查会话版本（密码或状态变更后强制重新登录）
        if (($payload['session_version'] ?? 0) !== JwtService::getSessionVersion('client', (int)$payload['id'])) {
            return json(['status' => 401, 'messages' => lang('password_changed_relogin'), 'time' => time()], 401);
        }

        // 检查密码是否已变更（兼容旧 Token）
        $pwdChanged = Cache::get('client_pwd_changed:' . $payload['id']);
        if ($pwdChanged && $pwdChanged >= ($payload['nbf'] ?? 0)) {
            return json(['status' => 401, 'messages' => lang('password_changed_relogin'), 'time' => time()], 401);
        }

        // 验证客户状态
        $client = Db::name('client')->where('id', $payload['id'])->find();
        if (!$client || $client['status'] != 1) {
            return json(['status' => 401, 'messages' => lang('account_disabled'), 'time' => time()], 401);
        }

        // 维护模式检查
        if ($this->isMaintenanceMode()) {
            return json(['status' => 503, 'messages' => lang('maintenance_mode'), 'time' => time()], 503);
        }

        // 挂载认证信息
        $request->clientId = (int)$payload['id'];
        $request->clientName = $payload['name'] ?? '';

        // 更新最后活动时间
        Db::name('client')->where('id', $payload['id'])->update([
            'last_action_time' => time(),
        ]);

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

    private function isMaintenanceMode(): bool
    {
        return (bool)Cache::remember('system:maintenance_mode', function () {
            $val = Db::name('configuration')->where('setting', 'maintenance_mode')->value('value');
            return $val === '1';
        }, 60);
    }
}