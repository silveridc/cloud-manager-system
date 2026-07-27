<?php

namespace app\http\middleware;

use app\common\service\JwtService;
use app\admin\model\AdminRoleLinkModel;
use app\admin\model\AdminModel;
use app\admin\model\AdminRuleModel;
use think\facade\Cache;
use think\Request;
use think\Response;

/**
 * 管理员认证中间件
 * @desc 管理员认证中间件
 * @uses \app\http\middleware\AdminAuth
 */
class AdminAuth
{
    /**
     * @title 管理员认证验证
     * @desc 管理员认证验证
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
        if (!Cache::get('admin_token:' . sha1($token))) {
            return json(['status' => 401, 'messages' => lang('token_expired'), 'time' => time()], 401);
        }

        // 解码 JWT
        $payload = JwtService::decodeAdmin($token);
        if (!$payload) {
            return json(['status' => 401, 'messages' => lang('token_invalid'), 'time' => time()], 401);
        }

        // 检查密码是否已变更（token 失效）
        $pwdChanged = Cache::get('admin_pwd_changed:' . $payload['id']);
        if ($pwdChanged && $pwdChanged > ($payload['nbf'] ?? 0)) {
            return json(['status' => 401, 'messages' => lang('password_changed_relogin'), 'time' => time()], 401);
        }

        // 验证管理员状态
        $admin = (new AdminModel())->where('id', $payload['id'])->find();
        if (!$admin || $admin['status'] != 1) {
            return json(['status' => 401, 'messages' => lang('account_disabled'), 'time' => time()], 401);
        }

        // RBAC 权限检查
        if (!$this->checkPermission($payload['id'], $request)) {
            return json(['status' => 403, 'messages' => lang('permission_denied'), 'time' => time()], 403);
        }

        // 挂载认证信息到 request
        $request->adminId = (int)$payload['id'];
        $request->adminName = $payload['name'] ?? '';

        // 更新最后活动时间
        (new AdminModel())->where('id', $payload['id'])->update([
            'last_action_time' => time(),
        ]);

        return $next($request);
    }

    /**
     * 从请求头提取 Bearer Token
     */
    private function extractToken(Request $request): string
    {
        $auth = $request->header('Authorization', '');
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return '';
    }

    /**
     * RBAC 权限检查
     */
    private function checkPermission(int $adminId, Request $request): bool
    {
        // 超级管理员跳过权限检查
        if ($adminId === 1) {
            return true;
        }

        $Cache = 'admin_rules:' . $adminId;
        $rules = Cache::get($Cache);

        if ($rules === null) {
            // 从数据库加载权限规则
            $roleIds = (new AdminRoleLinkModel())->where('admin_id', $adminId)
                ->column('role_id');

            if (empty($roleIds)) {
                return false;
            }

            $rules = (new AdminRuleModel())->whereIn('role_id', $roleIds)
                ->column('name');

            Cache::set($Cache, $rules, 7200); // 缓存2小时
        }
        $controller = $request->controller();
        $action = $request->action();

        return in_array(strtolower($controller . '/' . $action), array_map('strtolower', $rules));
    }
}