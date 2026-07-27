<?php

namespace app\home\logic;

use think\facade\Cache;

/**
 * 安全验证逻辑
 * @desc 安全验证逻辑（短信/邮箱验证码发送与校验）
 * @uses \app\home\logic\SecurityVerifyLogic
 */
class SecurityVerifyLogic
{
    /**
     * 单个验证码最大尝试次数
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * 发送手机验证码
     * @author zhaoyj
     * @version v1
     * @param string $phoneCode - 手机区号
     * @param string $phone - 手机号码
     * @param string $action - 业务动作标识（如register、reset等）
     * @return array|void
     */
    public function sendPhoneCode(string $phoneCode, string $phone, string $action)
    {
        $cacheKey = "sms_code:{$action}:{$phoneCode}{$phone}";

        // 60 秒内不能重复发送
        if (Cache::get($cacheKey . ':lock')) {
            return [
                'status' => 400,
                'messages' => lang('code_send_too_frequently'),
                'time' => time(),
            ];
        }

        $code = (string)random_int(100000, 999999);

        // 缓存验证码 5 分钟
        Cache::set($cacheKey, $code, 300);
        Cache::set($cacheKey . ':lock', 1, 60);
        // 重置尝试计数器
        Cache::set($cacheKey . ':attempts', 0, 300);

        // TODO: 调用短信插件发送验证码
    }

    /**
     * 发送邮箱验证码
     * @author zhaoyj
     * @version v1
     * @param string $email - 邮箱地址
     * @param string $action - 业务动作标识（如register、reset等）
     * @return array|void
     */
    public function sendEmailCode(string $email, string $action)
    {
        $cacheKey = "email_code:{$action}:{$email}";

        if (Cache::get($cacheKey . ':lock')) {
            return [
                'status' => 400,
                'messages' => lang('code_send_too_frequently'),
                'time' => time(),
            ];
        }

        $code = (string)random_int(100000, 999999);

        Cache::set($cacheKey, $code, 300);
        Cache::set($cacheKey . ':lock', 1, 60);
        // 重置尝试计数器
        Cache::set($cacheKey . ':attempts', 0, 300);

        // TODO: 调用邮件插件发送验证码
    }

    /**
     * 验证手机验证码，最多允许5次尝试，超过后删除验证码强制重新发送
     * @author zhaoyj
     * @version v1
     * @param string $phoneCode - 手机区号
     * @param string $phone - 手机号码
     * @param string $action - 业务动作标识
     * @param string $code - 验证码
     * @return bool|array
     */
    public function verifyPhoneCode(string $phoneCode, string $phone, string $action, string $code): bool|array
    {
        $cacheKey = "sms_code:{$action}:{$phoneCode}{$phone}";

        $result = $this->checkAttempts($cacheKey);
        if (is_array($result)) {
            return $result;
        }

        $cached = Cache::get($cacheKey);

        if (!$cached || $cached !== $code) {
            $this->incrementAttempts($cacheKey);
            return [
                'status' => 400,
                'messages' => lang('code_invalid'),
                'time' => time(),
            ];
        }

        Cache::delete($cacheKey);
        Cache::delete($cacheKey . ':attempts');
        return true;
    }

    /**
     * 验证邮箱验证码，最多允许5次尝试，超过后删除验证码强制重新发送
     * @author zhaoyj
     * @version v1
     * @param string $email - 邮箱地址
     * @param string $action - 业务动作标识
     * @param string $code - 验证码
     * @return bool|array
     */
    public function verifyEmailCode(string $email, string $action, string $code): bool|array
    {
        $cacheKey = "email_code:{$action}:{$email}";

        $result = $this->checkAttempts($cacheKey);
        if (is_array($result)) {
            return $result;
        }

        $cached = Cache::get($cacheKey);

        if (!$cached || $cached !== $code) {
            $this->incrementAttempts($cacheKey);
            return [
                'status' => 400,
                'messages' => lang('code_invalid'),
                'time' => time(),
            ];
        }

        Cache::delete($cacheKey);
        Cache::delete($cacheKey . ':attempts');
        return true;
    }

    /**
     * 检查验证尝试次数是否已超过上限，超过则删除验证码并抛出异常
     * @author zhaoyj
     * @version v1
     * @param string $cacheKey - 缓存键名
     * @return array|null 超过上限返回错误数组，否则null
     */
    private function checkAttempts(string $cacheKey)
    {
        $attempts = (int)Cache::get($cacheKey . ':attempts');
        if ($attempts >= self::MAX_ATTEMPTS) {
            // 超过最大尝试次数，删除验证码强制重新发送
            Cache::delete($cacheKey);
            Cache::delete($cacheKey . ':attempts');
            return [
                'status' => 400,
                'messages' => lang('code_attempts_exceeded'),
                'time' => time(),
            ];
        }
    }

    /**
     * 验证失败时递增尝试计数器
     * @author zhaoyj
     * @version v1
     * @param string $cacheKey - 缓存键名
     * @return void
     */
    private function incrementAttempts(string $cacheKey): void
    {
        $attempts = (int)Cache::get($cacheKey . ':attempts');
        Cache::set($cacheKey . ':attempts', $attempts + 1, 300);
    }
}