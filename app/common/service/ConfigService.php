<?php

namespace app\common\service;

use think\facade\Cache;
use think\facade\Db;

/**
 * 配置服务
 * @desc 配置服务
 * @uses \app\common\service\ConfigService
 */
class ConfigService
{
    private static array $local = [];

    /**
     * 获取配置
     * @desc 获取配置
     * @author zhaoyj
     * @version v1
     * @param string $setting
     * @param mixed $default
     * @return mixed
     */
    public static function GetConfig(string $setting, mixed $default = ''): mixed
    {
        // 优先内存缓存
        if (isset(self::$local[$setting])) {
            return self::$local[$setting];
        }

        // Redis 缓存
        $cacheKey = 'system_config:' . $setting;
        $value = Cache::get($cacheKey);

        if ($value === null) {
            $value = Db::name('configuration')
                ->where('setting', $setting)
                ->value('value');
            $value = $value ?? $default;
            Cache::set($cacheKey, $value, 3600);
        }

        self::$local[$setting] = $value;
        return $value;
    }

    /**
     * 批量获取配置
     * @desc 批量获取配置
     * @author zhaoyj
     * @version v1
     * @param array $settings
     * @return array
     */
    public static function GetConfigBatch(array $settings): array
    {
        $result = [];
        $missing = [];

        foreach ($settings as $setting) {
            if (isset(self::$local[$setting])) {
                $result[$setting] = self::$local[$setting];
            } else {
                $missing[] = $setting;
            }
        }

        if (!empty($missing)) {
            $rows = Db::name('configuration')
                ->whereIn('setting', $missing)
                ->column('value', 'setting');

            foreach ($missing as $setting) {
                $value = $rows[$setting] ?? '';
                self::$local[$setting] = $value;
                $result[$setting] = $value;
                Cache::set('system_config:' . $setting, $value, 3600);
            }
        }

        return $result;
    }

    /**
     * 设置配置
     * @desc 设置配置
     * @author zhaoyj
     * @version v1
     * @param string $setting
     * @param mixed $value
     * @return void
     * @throws \think\db\exception\DbException
     */
    public static function SetConfig(string $setting, mixed $value): void
    {
        Db::name('configuration')->where('setting', $setting)->update(['value' => $value]);
        self::$local[$setting] = $value;
        Cache::set('system_config:' . $setting, $value, 3600);
    }

    /**
     * 刷新缓存
     * @desc 刷新缓存
     * @author zhaoyj
     * @version v1
     * @param string $setting
     * @return void
     */
    public static function FlushCache(string $setting = ''): void
    {
        if ($setting) {
            unset(self::$local[$setting]);
            Cache::delete('system_config:' . $setting);
        } else {
            self::$local = [];
        }
    }
}