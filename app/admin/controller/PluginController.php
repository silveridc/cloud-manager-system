<?php
namespace app\admin\controller;

use app\admin\logic\PluginManager;

/**
 * 插件控制器
 * @desc 插件控制器
 * @uses \app\admin\controller\PluginController
 */
class PluginController extends AdminBaseController
{
    protected PluginManager $pluginManager;

    protected function initialize()
    {
        parent::initialize();
        $this->pluginManager = app(PluginManager::class);
    }

    /**
     * @title 获取插件列表
     * @desc 获取插件列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/plugin
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetPluginList()
    {
        $type = $this->request->param('type', '');
        $data = $this->pluginManager->GetPluginList($type);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取插件详情
     * @desc 获取插件详情
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/plugin/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetPluginInfo(int $id)
    {
        $data = $this->pluginManager->GetPluginInfo($id);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('plugin_not_found'),
                'time' => time(),
            ];
            return json($result, 404);
        }
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Install
     * @desc Install
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/plugin/install
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function InstallPlugin()
    {
        $type = $this->request->param('type', '');
        $name = $this->request->param('name', '');

        // 输入不能为空
        if (empty($type) || empty($name)) {
            $result = [
                'status' => 400,
                'messages' => 'type 和 name 参数不能为空',
                'time' => time(),
            ];
            return json($result, 400);
        }

        // 严格校验格式：仅允许小写字母、数字、下划线，以字母开头
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $type)) {
            $result = [
                'status' => 400,
                'messages' => 'type 格式不合法，仅允许小写字母、数字和下划线，且以字母开头',
                'time' => time(),
            ];
            return json($result, 400);
        }
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $name)) {
            $result = [
                'status' => 400,
                'messages' => 'name 格式不合法，仅允许小写字母、数字和下划线，且以字母开头',
                'time' => time(),
            ];
            return json($result, 400);
        }

        // 限制长度，防止过长输入
        if (strlen($type) > 64 || strlen($name) > 64) {
            $result = [
                'status' => 400,
                'messages' => 'type 或 name 长度不能超过64个字符',
                'time' => time(),
            ];
            return json($result, 400);
        }

        $this->pluginManager->InstallPlugin($type, $name);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Uninstall
     * @desc Uninstall
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/plugin/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UninstallPlugin(int $id)
    {
        $this->pluginManager->UninstallPlugin($id);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Toggle Status
     * @desc Toggle Status
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/plugin/:id/status
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function TogglePluginStatus(int $id)
    {
        $this->pluginManager->TogglePluginStatus($id);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}