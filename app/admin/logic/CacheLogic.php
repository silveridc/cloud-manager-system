<?php
namespace app\admin\logic;

use think\facade\Cache;

/**
 * 缓存逻辑
 * @desc 缓存逻辑
 * @uses \app\admin\logic\CacheLogic
 */
class CacheLogic
{
    /**
     * 清除所有缓存
     * @desc 清除配置、路由、视图缓存，但保留认证token不被清除，防止所有用户被强制下线
     * @author zhaoyj
     * @version v1
     * @return void
     */
    public function clearAll(): void
    {
        // 不使用 Cache::clear() 以避免清除 admin_token:* 和 client_token:* 等认证缓存
        // 逐类清除业务缓存
        $this->clearConfig();
        $this->clearRoute();
        $this->clearView();
    }

    /**
     * 清除配置缓存
     * @author zhaoyj
     * @version v1
     * @return void
     */
    public function clearConfig(): void
    {
        // 清除系统配置缓存
        $keys = Cache::getTagItems('system_config');
        if ($keys) {
            foreach ($keys as $key) {
                Cache::delete($key);
            }
        }
        \app\common\service\ConfigService::FlushCache();
    }

    /**
     * 清除路由缓存
     * @author zhaoyj
     * @version v1
     * @return void
     */
    public function clearRoute(): void
    {
        $routeCache = runtime_path() . 'route' . DIRECTORY_SEPARATOR;
        if (is_dir($routeCache)) {
            $files = glob($routeCache . '*.php');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }

    /**
     * 清除视图缓存
     * @author zhaoyj
     * @version v1
     * @return void
     */
    public function clearView(): void
    {
        $viewCache = runtime_path() . 'temp' . DIRECTORY_SEPARATOR;
        if (is_dir($viewCache)) {
            $files = glob($viewCache . '*.php');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
}