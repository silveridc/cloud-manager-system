<?php

namespace app\common\contract;

/**
 * 插件基础实现类
 * @desc 插件基础实现类
 * @uses \app\common\contract\PluginInterface
 */
interface PluginInterface
{
    /**
     * @title 获取插件信息
     * @desc 获取插件信息
     * @author zhaoyj
     * @version v1
     * @return array ['name', 'title', 'description', 'author', 'version']
     */
    public function info(): array;

    //必须实现安装
    public function install();

    //必须卸载插件方法
    public function uninstall();
}