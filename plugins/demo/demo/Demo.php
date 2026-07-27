<?php

namespace plugins\demo\demo;

use app\common\contract\PluginInterface;

/**
 * Demo 示例插件
 * @desc 示例插件，展示插件基本结构
 */
class Demo implements PluginInterface
{
    /**
     * 获取插件信息
     */
    public function info(): array
    {
        return [
            'name'        => 'demo',
            'title'       => '示例插件',
            'description' => '这是一个示例插件，仅供参考',
            'author'      => 'zhaoyj',
            'version'     => '1.0.0',
        ];
    }

    /**
     * 安装插件
     */
    public function install(): void
    {
        // 安装时的初始化操作，比如创建数据表、写入默认配置等
    }

    /**
     * 卸载插件
     */
    public function uninstall(): void
    {
        // 卸载时的清理操作，比如删除数据表等
    }
}
