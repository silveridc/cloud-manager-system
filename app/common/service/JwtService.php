<?php

namespace app\common\service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\admin\model\AdminModel;
use app\common\model\ClientModel;
use think\facade\Cache;

/**
 * JWT认证服务
 * @desc JWT认证服务
 * @uses \app\common\service\JwtService
 */
class JwtService
{
    /**
     * 签发admin的token
     * @author zhaoyj
     * @version v1
     * @param array $info
     * @param bool $rememberPassword
     * @return string
     */
    public static function createAdmin(array $info, bool $rememberPassword = false): string
    {
        $expire = $rememberPassword ? 86400 * 7 : 7200; // 7天 or 2小时
        return self::create($info, $expire, 'admin');
    }

    /**
     * 签发客户端 Token
     */
    public static function createClient(array $info, bool $rememberPassword = false): string
    {
        $expire = $rememberPassword ? 86400 * 7 : 7200;
        return self::create($info, $expire, 'client');
    }

    /**
     * 解码管理端 Token
     */
    public static function decodeAdmin(string $token): ?array
    {
        return self::decode($token, 'admin');
    }

    /**
     * 解码客户端 Token
     */
    public static function decodeClient(string $token): ?array
    {
        return self::decode($token, 'client');
    }

    /**
     * 注销管理端 Token
     */
    public static function revokeAdmin(string $token): void
    {
        Cache::delete('admin_token:' . sha1($token));
    }

    /**
     * 注销客户端 Token
     */
    public static function revokeClient(string $token): void
    {
        Cache::delete('client_token:' . sha1($token));
    }

    /**
     * 使某个主体的全部 Token 失效。
     */
    public static function invalidateSessions(string $type, int $id): void
    {
        if ($type === 'admin') {
            (new AdminModel())->where('id', $id)->inc('session_version')->update();
            return;
        }

        if ($type === 'client') {
            (new ClientModel())->where('id', $id)->inc('session_version')->update();
            return;
        }

        throw new \InvalidArgumentException('Unsupported token type');
    }

    /**
     * 获取主体当前会话版本。
     */
    public static function getSessionVersion(string $type, int $id): int
    {
        if ($type === 'admin') {
            return (int)(new AdminModel())->where('id', $id)->value('session_version');
        }

        if ($type === 'client') {
            return (int)(new ClientModel())->where('id', $id)->value('session_version');
        }

        throw new \InvalidArgumentException('Unsupported token type');
    }

    /**
     * 创建 JWT
     */
    private static function create(array $info, int $expire, string $type): string
    {
        $key = config('jwt.' . $type . '_key');
        $now = time();

        $payload = [
            'iss' => config('app.app_name', 'cloud'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $expire,
            'id' => $info['id'],
            'session_version' => self::getSessionVersion($type, (int)$info['id']),
            'name' => $info['name'] ?? '',
            'type' => $type,
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        // 缓存 token 用于主动失效
        $key = $type . '_token:' . sha1($token);
        Cache::set($key, $info['id'], $expire);

        return $token;
    }

    /**
     * 解码 JWT
     */
    private static function decode(string $token, string $type): ?array
    {
        try {
            $key = config('jwt.' . $type . '_key');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return (array)$decoded;
        } catch (\Throwable) {
            return null;
        }
    }
}