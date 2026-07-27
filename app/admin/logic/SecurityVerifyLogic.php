<?php
namespace app\admin\logic;

use think\facade\Cache;

/**
 * 安全验证逻辑
 * @desc 安全验证逻辑
 * @uses \app\admin\logic\SecurityVerifyLogic
 */
class SecurityVerifyLogic
{
    /**
     * 发送手机验证码（60秒内不可重复发送，验证码有效期5分钟）
     * @author zhaoyj
     * @version v1
     * @param string $phoneCode - 国际区号
     * @param string $phone - 手机号码
     * @param string $action - 业务动作标识
     * @return void
     * @throws \think\exception\ValidateException - 60秒内重复发送时抛出
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

        $code = (string)mt_rand(100000, 999999);

        // 缓存验证码 5 分钟
        Cache::set($cacheKey, $code, 300);
        Cache::set($cacheKey . ':lock', 1, 60);

        // TODO: 调用短信插件发送验证码
    }

    /**
     * 发送邮箱验证码（60秒内不可重复发送，验证码有效期5分钟）
     * @author zhaoyj
     * @version v1
     * @param string $email - 邮箱地址
     * @param string $action - 业务动作标识
     * @return void
     * @throws \think\exception\ValidateException - 60秒内重复发送时抛出
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

        $code = (string)mt_rand(100000, 999999);

        Cache::set($cacheKey, $code, 300);
        Cache::set($cacheKey . ':lock', 1, 60);

        // TODO: 调用邮件插件发送验证码
    }

    /**
     * 验证手机验证码（验证成功后自动删除缓存的验证码）
     * @author zhaoyj
     * @version v1
     * @param string $phoneCode - 国际区号
     * @param string $phone - 手机号码
     * @param string $action - 业务动作标识
     * @param string $code - 用户输入的验证码
     * @return bool - 验证成功返回true
     * @throws \think\exception\ValidateException - 验证码无效或不匹配时抛出
     */
    public function verifyPhoneCode(string $phoneCode, string $phone, string $action, string $code)
    {
        $cacheKey = "sms_code:{$action}:{$phoneCode}{$phone}";
        $cached = Cache::get($cacheKey);

        if (!$cached || $cached !== $code) {
            return [
                'status' => 400,
                'messages' => lang('code_invalid'),
                'time' => time(),
            ];
        }

        Cache::delete($cacheKey);
        return true;
    }

    /**
     * 验证邮箱验证码（验证成功后自动删除缓存的验证码）
     * @author zhaoyj
     * @version v1
     * @param string $email - 邮箱地址
     * @param string $action - 业务动作标识
     * @param string $code - 用户输入的验证码
     * @return bool - 验证成功返回true
     * @throws \think\exception\ValidateException - 验证码无效或不匹配时抛出
     */
    public function verifyEmailCode(string $email, string $action, string $code)
    {
        $cacheKey = "email_code:{$action}:{$email}";
        $cached = Cache::get($cacheKey);

        if (!$cached || $cached !== $code) {
            return [
                'status' => 400,
                'messages' => lang('code_invalid'),
                'time' => time(),
            ];
        }

        Cache::delete($cacheKey);
        return true;
    }
}