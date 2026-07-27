<?php
namespace app\admin\logic;

use app\common\model\ClientModel;
use app\common\model\ClientLoginModel;
use think\facade\Event;
use app\common\service\JwtService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;

/**
 * 客户认证逻辑
 * @desc 客户认证逻辑
 * @uses \app\admin\logic\ClientAuthLogic
 */
class ClientAuthLogic
{
    /**
     * 客户登录（支持邮箱、手机号、用户名）
     * @author zhaoyj
     * @version v1
     * @param string $account - 登录账号（邮箱/手机号/用户名）
     * @param string $password - 登录密码
     * @param bool $rememberPassword - 是否记住密码（延长token有效期）
     * @return array
     */
    public function login(string $account, string $password, bool $rememberPassword = false): array
    {
        $clientModel = new ClientModel();
        $clientLoginModel = new ClientLoginModel();

        // 支持邮箱或手机号登录
        $client = $clientModel
            ->where(function ($query) use ($account) {
                $query->where('email', $account)
                    ->whereOr('phone', $account)
                    ->whereOr('username', $account);
            })
            ->find();

        if (!$client) {
            return [
                'status' => 404,
                'messages' => lang('account_not_found'),
                'time' => time(),
            ];
        }

        if ($client['status'] != 1) {
            return [
                'status' => 403,
                'messages' => lang('account_disabled'),
                'time' => time(),
            ];
        }

        if (cmf_password($password) !== $client['password']) {
            return [
                'status' => 400,
                'messages' => lang('password_error'),
                'time' => time(),
            ];
        }

        // 签发 token
        $token = JwtService::createClient([
            'id' => $client['id'],
            'name' => $client['username'],
        ], $rememberPassword);

        // 记录登录
        $clientModel->where('id', $client['id'])->update([
            'last_login_time' => time(),
            'last_login_ip' => request()->ip(),
            'last_action_time' => time(),
        ]);

        $clientLoginModel->save([
            'client_id' => $client['id'],
            'ip' => request()->ip(),
            'create_time' => time(),
        ]);

        Event::trigger('after_client_login', ['client_id' => $client['id']]);

        return [
            'jwt' => $token,
            'id' => $client['id'],
            'name' => $client['username'],
        ];
    }

    /**
     * 客户注册并自动登录
     * @author zhaoyj
     * @version v1
     * @param array $params .country_id - 国家ID（可选）
     * @return array
     * @throws \Throwable 数据库事务异常时抛出
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function register(array $params): array
    {
        $clientModel = new ClientModel();

        // 检查是否已存在
        $exists = $clientModel
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
        $client->startTrans();
        try {
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

            // 自动登录
            $token = JwtService::createClient([
                'id' => $client->id,
                'name' => $client->getAttr('username'),
            ]);

            $client->commit();

            return [
                'jwt' => $token,
                'id' => (int)$client->id,
                'name' => $client->getAttr('username'),
            ];
        } catch (\Throwable $e) {
            $client->rollback();
            throw $e;
        }
    }

    /**
     * 客户退出登录（吊销JWT令牌）
     * @author zhaoyj
     * @version v1
     * @param string $token - 待吊销的JWT令牌
     * @return void
     */
    public function logout(string $token): void
    {
        JwtService::revokeClient($token);
    }

    /**
     * 修改客户密码
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param string $oldPassword - 旧密码
     * @param string $newPassword - 新密码
     * @return array|void
     */
    public function changePassword(int $clientId, string $oldPassword, string $newPassword)
    {
        $clientModel = new ClientModel();

        $client = $clientModel->where('id', $clientId)->find();
        if (!$client) {
            return [
                'status' => 404,
                'messages' => lang('client_not_found'),
                'time' => time(),
            ];
        }

        if (cmf_password($oldPassword) !== $client['password']) {
            return [
                'status' => 400,
                'messages' => lang('old_password_error'),
                'time' => time(),
            ];
        }

        $clientModel->where('id', $clientId)->update([
            'password' => cmf_password($newPassword),
        ]);

        // 标记密码变更
        Cache::set('client_pwd_changed:' . $clientId, time(), 86400 * 7);
    }
}
