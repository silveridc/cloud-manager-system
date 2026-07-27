<?php

namespace app\home\logic;

use think\facade\Event;
use app\common\service\JwtService;
use app\common\model\ClientLoginModel;
use app\common\model\ClientModel;
use think\facade\Cache;

/**
 * 客户认证逻辑
 * @desc 客户认证逻辑
 * @uses \app\home\logic\ClientAuthLogic
 */
class ClientAuthLogic
{
    protected ClientModel $clientModel;
    protected ClientLoginModel $loginModel;

    public function __construct(ClientModel $clientModel, ClientLoginModel $loginModel)
    {
        $this->clientModel = $clientModel;
        $this->loginModel = $loginModel;
    }

    /**
     * 客户登录
     * @author zhaoyj
     * @version v1
     * @param string $account - 登录账号(邮箱/手机号/用户名)
     * @param string $password - 登录密码
     * @param bool $rememberPassword - 是否记住密码(延长token有效期)
     * @return array
     * @return array jwt - JWT令牌
     * @return array id - 客户ID
     * @return array name - 客户用户名
     */
    public function login(string $account, string $password, bool $rememberPassword = false): array
    {
        // 登录暴力破解防护：同一 IP+账号 5次失败后锁定15分钟
        $result = $this->checkLoginAttempts($account);
        if (is_array($result)) {
            return $result;
        }

        $client = $this->clientModel
            ->where(function ($query) use ($account) {
                $query->where('email', $account)
                    ->whereOr('phone', $account)
                    ->whereOr('username', $account);
            })
            ->find();

        if (!$client) {
            $this->recordFailedAttempt($account);
            return [
                'status' => 404,
                'messages' => lang('account_not_found'),
                'time' => time(),
            ];
        }

        if ($client->getAttr('status') != 1) {
            return [
                'status' => 403,
                'messages' => lang('account_disabled'),
                'time' => time(),
            ];
        }

        if (cmf_password($password) !== $client->getAttr('password')) {
            $this->recordFailedAttempt($account);
            return [
                'status' => 400,
                'messages' => lang('password_error'),
                'time' => time(),
            ];
        }

        // 登录成功，清除失败计数
        $this->clearFailedAttempts($account);

        $token = JwtService::createClient([
            'id' => $client->getAttr('id'),
            'name' => $client->getAttr('username'),
        ], $rememberPassword);

        $client->save([
            'last_login_time' => time(),
            'last_login_ip' => request()->ip(),
            'last_action_time' => time(),
        ]);

        $login = new ClientLoginModel();
        $login->save([
            'client_id' => $client->getAttr('id'),
            'ip' => request()->ip(),
        ]);

        Event::trigger('after_client_login', ['client_id' => $client->getAttr('id')]);

        return [
            'jwt' => $token,
            'id' => $client->getAttr('id'),
            'name' => $client->getAttr('username'),
        ];
    }

    /**
     * 检查登录尝试次数
     * @author zhaoyj
     * @version v1
     * @param string $account - 登录账号
     * @return array|null 登录尝试超限返回错误数组，否则null
     */
    private function checkLoginAttempts(string $account)
    {
        $cacheKey = 'client_login_attempts:' . request()->ip() . ':' . $account;
        $attempts = (int)Cache::get($cacheKey, 0);

        if ($attempts >= 5) {
            return [
                'status' => 400,
                'messages' => lang('login_attempts_exceeded'),
                'time' => time(),
            ];
        }
    }

    /**
     * 记录登录失败次数，15分钟后自动过期
     * @author zhaoyj
     * @version v1
     * @param string $account - 登录账号
     * @return void
     */
    private function recordFailedAttempt(string $account): void
    {
        $cacheKey = 'client_login_attempts:' . request()->ip() . ':' . $account;
        $attempts = (int)Cache::get($cacheKey, 0);
        Cache::set($cacheKey, $attempts + 1, 900); // 15分钟 = 900秒
    }

    /**
     * 清除登录失败计数
     * @author zhaoyj
     * @version v1
     * @param string $account - 登录账号
     * @return void
     */
    private function clearFailedAttempts(string $account): void
    {
        $cacheKey = 'client_login_attempts:' . request()->ip() . ':' . $account;
        Cache::delete($cacheKey);
    }

    /**
     * 客户注册
     * @author zhaoyj
     * @version v1
     * @param array $params - 注册参数
     * @param array params.username - 用户名(可选)
     * @param array params.email - 邮箱(可选)
     * @param array params.phone - 手机号(可选)
     * @param array params.phone_code - 手机区号(可选)
     * @param array params.password - 密码
     * @param array params.language - 语言(可选，默认zh-cn)
     * @param array params.country_id - 国家ID(可选，默认0)
     * @return array
     * @return array jwt - JWT令牌
     * @return array id - 新注册客户ID
     * @return array name - 客户用户名
     */
    public function register(array $params): array
    {
        $exists = $this->clientModel
            ->where(function ($query) use ($params) {
                if (!empty($params['email'])) {
                    $query->whereOr('email', $params['email']);
                }
                if (!empty($params['phone'])) {
                    $query->whereOr('phone', $params['phone']);
                }
                if (!empty($params['username'])) {
                    $query->whereOr('username', $params['username']);
                }
            })
            ->find();

        if ($exists) {
            return [
                'status' => 400,
                'messages' => lang('account_already_exists'),
                'time' => time(),
            ];
        }

        $client = new ClientModel();
        $client->save([
            'username' => $params['username'] ?? $params['email'] ?? $params['phone'],
            'email' => $params['email'] ?? '',
            'phone_code' => $params['phone_code'] ?? '',
            'phone' => $params['phone'] ?? '',
            'password' => cmf_password($params['password']),
            'status' => 1,
            'credit' => 0,
            'language' => $params['language'] ?? 'zh-cn',
            'country_id' => $params['country_id'] ?? 0,
        ]);

        Event::trigger('after_client_register', ['client_id' => $client->id]);

        $token = JwtService::createClient([
            'id' => $client->id,
            'name' => $client->getAttr('username'),
        ]);

        return [
            'jwt' => $token,
            'id' => (int)$client->id,
            'name' => $client->getAttr('username'),
        ];
    }

    /**
     * 客户退出登录
     * @author zhaoyj
     * @version v1
     * @param string $token - 当前JWT令牌
     * @return void
     */
    public function logout(string $token): void
    {
        JwtService::revokeClient($token);
    }

    /**
     * 修改密码
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param string $oldPassword - 旧密码
     * @param string $newPassword - 新密码
     * @return array|void
     */
    public function changePassword(int $clientId, string $oldPassword, string $newPassword)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return [
                'status' => 404,
                'messages' => lang('client_not_found'),
                'time' => time(),
            ];
        }

        if (cmf_password($oldPassword) !== $client->getAttr('password')) {
            return [
                'status' => 400,
                'messages' => lang('old_password_error'),
                'time' => time(),
            ];
        }

        $client->save([
            'password' => cmf_password($newPassword),
        ]);

        Cache::set('client_pwd_changed:' . $clientId, time(), 86400 * 7);
    }
}