<?php
namespace app\admin\logic;

use app\common\model\ConfigurationModel;
use app\common\service\ConfigService;

/**
 * 系统配置逻辑类
 * @uses \app\admin\logic\ConfigurationLogic
 */
class ConfigurationLogic
{
    /**
     * 按分组获取配置
     * @author zhaoyj
     * @version v1
     * @param string $group - 配置分组名称
     * @return array
     */
    public function getGroup(string $group): array
    {
        $ConfigurationModel = new ConfigurationModel();
        return $ConfigurationModel
            ->where('group', $group)
            ->column('value', 'setting');
    }

    /**
     * 获取所有配置
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function getAll(): array
    {
        $ConfigurationModel = new ConfigurationModel();
        $configs = $ConfigurationModel
            ->column('value', 'setting');

        // 脱敏处理：隐藏敏感配置值
        $sensitiveKeys = ['smtp_password', 'api_secret', 'gateway_key', 'gateway_secret', 'sms_key', 'oss_secret'];
        foreach ($configs as $key => &$value) {
            foreach ($sensitiveKeys as $sensitive) {
                if (stripos($key, $sensitive) !== false && !empty($value)) {
                    $value = '******' . mb_substr($value, -4);
                    break;
                }
            }
        }

        return $configs;
    }

    /**
     * 批量更新配置
     * @author zhaoyj
     * @version v1
     * @param array $settings - 配置键值对
     * @return void
     */
    public function batchUpdate(array $settings): void
    {
        $ConfigurationModel = new ConfigurationModel();
        foreach ($settings as $key => $value) {
            $ConfigurationModel
                ->where('setting', $key)
                ->update(['value' => $value]);
            ConfigService::FlushCache($key);
        }
    }
}