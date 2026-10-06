<?php
namespace app\admin\logic;

use app\admin\model\AdminModel;
use app\common\service\JwtService;
use think\facade\Cache;
/**
 * 管理员登录逻辑类
 * @desc 管理员登录逻辑类
 * @author zhaoyj
 * @use \app\admin\logic\AdminAuthLogic
 */
class AdminAuthLogic
{
    /**
     * 管理员登录
     * @author zhaoyj
     * @version v1
     * @param string $Username - 管理员用户名
     * @param string $Password - 管理员密码
     * @param bool $RememberPassword - 是否记住密码（延长token有效期）
     * @return array
     * @return string jwt - 签发的JWT令牌
     * @return int id - 管理员ID
     * @return string name - 管理员用户名
     * @throws \think\exception\ValidateException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function AdminLogin(string $Username, string $Password, bool $RememberPassword = false)
    {
        // 登录暴力破解防护：同一 IP+账号 5次失败后锁定15分钟
        $lockResult = $this->checkLoginAttempts($Username);
        if (is_array($lockResult)) {
            return $lockResult;
        }

        $AdminModel = new AdminModel();

        $admin = $AdminModel->where('name', $Username)
            ->find();

        //管理员不存在
        if (!$admin) {
            $this->LoginFailCounts($Username);
            return ([
                'status' => 404,
                'messages' => lang('admin_not_found'),
                'time' => time(),
            ]);
        }
        // 管理员被禁用
        if ($admin['status'] != 1) {
            return ([
                'status' => 403,
                'messages' => lang('account_disabled'),
                'time' => time(),
            ]);
        }

        //管理员密码不正确
        if (cmf_password($Password) !== $admin['password']) {
            $this->LoginFailCounts($Username);
            return ([
                'status' => 401,
                'messages' => lang('password_error'),
                'time' => time(),
            ]);
        }

        // 登录成功，清除失败计数
        Cache::delete('admin_login_attempts:' . request()->ip() . ':' . $Username);

        // 签发 token
        $token = JwtService::createAdmin([
            'id' => $admin['id'],
            'name' => $admin['name'],
        ], $RememberPassword);

        // 记录登录
        $AdminModel->where('id', $admin['id'])->update([
            'last_login_time' => time(),
            'last_login_ip' => request()->ip(),
            'last_action_time' => time(),
        ]);
        return [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => ['jwt' => $token,'id' => $admin['id'],'name' => $admin['name'],],
            'time' => time(),
        ];
    }

    /**
     * 检查登录尝试次数
     * @author zhaoyj
     * @version v1
     * @param string $account - 账号名称
     * @return void
     * @throws \think\exception\ValidateException
     */
    private function checkLoginAttempts(string $account)
    {
        $cacheKey = 'admin_login_attempts:' . request()->ip() . ':' . $account;
        $attempts = (int)Cache::get($cacheKey, 0);

        if ($attempts >= 5) {
            return ([
                'status' => 401,
                'messages' => lang('password_error'),
                'time' => time(),
            ]);
        }
    }

    /**
     * 记录登录失败次数
     * @author zhaoyj
     * @version v1
     * @param string $account - 账号名称
     * @return void
     */
    private function LoginFailCounts(string $account)
    {
        $cacheKey = 'admin_login_attempts:' . request()->ip() . ':' . $account;
        $attempts = (int)Cache::get($cacheKey, 0);
        Cache::set($cacheKey, $attempts + 1, 900); // 15分钟 = 900秒
    }

    /**
     * 管理员修改密码
     * @author zhaoyj
     * @version v1
     * @param int $AdminID - 管理员ID
     * @param string $CurrentPassword - 当前密码（用于验证身份）
     * @param string $Password - 新密码
     * @return void
     * @throws \think\exception\ValidateException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function AdminChangePassword(int $AdminID, string $CurrentPassword, string $Password)
    {
        $AdminModel = new AdminModel();
        $admin = $AdminModel->where('id', $AdminID)->find();
        if (!$admin) {
            return ([
                'status' => 403,
                'messages' => lang('admin_not_found'),
                'time' => time(),
            ]);
        }
        if (cmf_password($CurrentPassword) !== $admin['password']) {
            return ([
                'status' => 403,
                'messages' => lang('old_password_error'),
                'time' => time(),
            ]);
        }
        $AdminModel->where('id', $AdminID)->update([
            'password' => cmf_password($Password),
        ]);
        JwtService::invalidateSessions('admin', $AdminID);
        return [
            'status' => 200,
            'messages' => lang('password_changed'),
            'time' => time(),
        ];
    }
}