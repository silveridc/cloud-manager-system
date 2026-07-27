<?php
namespace app\admin\logic;

use app\common\model\PluginModel;
use think\facade\Event;

/**
 * 插件管理器
 * @desc 插件管理器
 * @uses \app\admin\logic\PluginManager
 */
class PluginManager
{
    /**
     * 获取插件列表
     * @author zhaoyj
     * @version v1
     * @param string $type - 插件类型，为空则查询全部
     * @return array
     */
    public function GetPluginList(string $type = ''): array
    {
        $PluginModel = new PluginModel();
        $query = $PluginModel;
        if ($type) {
            $query = $query->where('type', $type);
        }
        return $query->order('id', 'asc')->select()->toArray();
    }

    /**
     * 获取插件详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 插件ID
     * @return array|null
     */
    public function GetPluginInfo(int $id): ?array
    {
        $PluginModel = new PluginModel();
        $record = $PluginModel->where('id', $id)->find();
        return $record ? $record->toArray() : null;
    }

    /**
     * 安装插件
     * @author zhaoyj
     * @version v1
     * @param string $type - 插件类型
     * @param string $name - 插件名称
     * @return array|null
     * @throws \Throwable
     */
    public function InstallPlugin(string $type, string $name)
    {
        $class = $this->resolvePluginClass($type, $name);
        if (!class_exists($class)) {
            return [
                'status' => 404,
                'messages' => lang('plugin_class_not_found'),
                'time' => time(),
            ];
        }

        $plugin = new $class();
        $info = $plugin->info();

        $PluginModel = new PluginModel();

        $PluginModel->startTrans();
        try {
            $plugin->install();

            $PluginModel->save([
                'type' => $type,
                'name' => $name,
                'title' => $info['title'] ?? $name,
                'version' => $info['version'] ?? '1.0.0',
                'author' => $info['author'] ?? '',
                'description' => $info['description'] ?? '',
                'status' => 1,
                'config' => '{}',
            ]);

            $PluginModel->commit();
        } catch (\Throwable $e) {
            $PluginModel->rollback();
            throw $e;
        }
    }

    /**
     * 卸载插件
     * @author zhaoyj
     * @version v1
     * @param int $id - 插件ID
     * @return array|null
     */
    public function UninstallPlugin(int $id)
    {
        $PluginModel = new PluginModel();
        $record = $PluginModel->where('id', $id)->find();
        if (!$record) {
            return [
                'status' => 404,
                'messages' => lang('plugin_not_found'),
                'time' => time(),
            ];
        }

        $class = $this->resolvePluginClass($record['type'], $record['name']);
        if (class_exists($class)) {
            $plugin = new $class();
            $plugin->uninstall();
        }

        $record->delete();
    }

    /**
     * 启用/禁用插件状态
     * @author zhaoyj
     * @version v1
     * @param int $id - 插件ID
     * @return array|null
     */
    public function TogglePluginStatus(int $id)
    {
        $PluginModel = new PluginModel();
        $record = $PluginModel->where('id', $id)->find();
        if (!$record) {
            return [
                'status' => 404,
                'messages' => lang('plugin_not_found'),
                'time' => time(),
            ];
        }

        $record->save([
            'status' => $record['status'] == 1 ? 0 : 1,
        ]);
    }

    /**
     * 注册所有已启用插件的Hook监听
     * @author zhaoyj
     * @version v1
     * @return void
     */
    public function registerHooks(): void
    {
        $PluginModel = new PluginModel();
        $plugins = $PluginModel->where('status', 1)->select()->toArray();
        $pluginsBase = realpath(root_path() . 'plugins');

        foreach ($plugins as $plugin) {
            $hooksFile = $this->getPluginPath($plugin['type'], $plugin['name']) . 'hooks.php';
            $realHooksFile = realpath($hooksFile);

            // 校验文件存在且在插件目录内
            if ($realHooksFile === false || $pluginsBase === false) {
                continue;
            }
            if (!str_starts_with($realHooksFile, $pluginsBase)) {
                continue;
            }
            if (!is_file($realHooksFile)) {
                continue;
            }

            $hooks = include $realHooksFile;
            if (is_array($hooks)) {
                foreach ($hooks as $event => $listeners) {
                    foreach ((array)$listeners as $listener) {
                        Event::listen($event, $listener);
                    }
                }
            }
        }
    }

    /**
     * 注册所有已启用插件的路由
     * @author zhaoyj
     * @version v1
     * @return void
     */
    public function registerRoutes(): void
    {
        $PluginModel = new PluginModel();
        $plugins = $PluginModel->where('status', 1)->select()->toArray();
        $pluginsBase = realpath(root_path() . 'plugins');

        foreach ($plugins as $plugin) {
            $routeFile = $this->getPluginPath($plugin['type'], $plugin['name']) . 'route.php';
            $realRouteFile = realpath($routeFile);

            // 校验文件存在且在插件目录内
            if ($realRouteFile === false || $pluginsBase === false) {
                continue;
            }
            if (!str_starts_with($realRouteFile, $pluginsBase)) {
                continue;
            }
            if (!is_file($realRouteFile)) {
                continue;
            }

            include $realRouteFile;
        }
    }

    /**
     * 调用插件方法
     * @author zhaoyj
     * @version v1
     * @param string $type - 插件类型
     * @param string $name - 插件名称
     * @param string $method - 方法名
     * @param array $params - 调用参数
     * @return mixed
     */
    public function call(string $type, string $name, string $method, array $params = []): mixed
    {
        // 校验方法名：仅允许小写字母开头，包含字母、数字、下划线
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $method)) {
            return [
                'status' => 400,
                'messages' => 'method 格式不合法，仅允许字母、数字和下划线，且以字母开头',
                'time' => time(),
            ];
        }

        // 禁止调用魔术方法和内部方法
        if (str_starts_with($method, '__')) {
            return [
                'status' => 400,
                'messages' => '不允许调用魔术方法',
                'time' => time(),
            ];
        }

        $class = $this->resolvePluginClass($type, $name);
        if (!class_exists($class)) {
            return [
                'status' => 404,
                'messages' => lang('plugin_class_not_found'),
                'time' => time(),
            ];
        }

        $plugin = new $class();
        if (!method_exists($plugin, $method)) {
            return [
                'status' => 404,
                'messages' => lang('plugin_method_not_found'),
                'time' => time(),
            ];
        }

        return $plugin->$method($params);
    }

    /**
     * 验证插件标识符格式
     * @desc 验证插件标识符格式，仅允许小写字母、数字、下划线
     * @author zhaoyj
     * @version v1
     * @param string $value - 待验证的标识符
     * @param string $label - 字段名称，用于错误提示
     * @return array|null
     */
    private function validateIdentifier(string $value, string $label)
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $value)) {
            return [
                'status' => 400,
                'messages' => "{$label} 格式不合法，仅允许小写字母、数字和下划线，且以字母开头",
                'time' => time(),
            ];
        }
    }

    /**
     * 解析插件主类名
     * @desc 解析插件主类名，包含严格的格式校验
     * @author zhaoyj
     * @version v1
     * @param string $type - 插件类型
     * @param string $name - 插件名称
     * @return string|array
     */
    private function resolvePluginClass(string $type, string $name): string|array
    {
        $this->validateIdentifier($type, 'type');
        $this->validateIdentifier($name, 'name');

        // 插件类命名规则：命名空间\类名 (类名为驼峰)
        $className = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $name)));
        return "\\{$type}\\{$name}\\{$className}";
    }

    /**
     * 获取插件目录路径
     * @desc 获取插件目录路径，校验路径在允许的插件目录内
     * @author zhaoyj
     * @version v1
     * @param string $type - 插件类型
     * @param string $name - 插件名称
     * @return string|array
     */
    private function getPluginPath(string $type, string $name): string|array
    {
        $this->validateIdentifier($type, 'type');
        $this->validateIdentifier($name, 'name');

        $pluginsBase = root_path() . 'plugins' . DIRECTORY_SEPARATOR;
        $resolved = realpath($pluginsBase . $type . DIRECTORY_SEPARATOR . $name);

        // 如果目录不存在，使用拼接路径但仍需确保无路径穿越
        if ($resolved === false) {
            $candidate = $pluginsBase . $type . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
            // 标识符已通过正则校验，不会含有 .. 或路径分隔符，安全返回
            return $candidate;
        }

        $resolvedBase = realpath($pluginsBase);
        if ($resolvedBase === false || !str_starts_with($resolved, $resolvedBase)) {
            return [
                'status' => 400,
                'messages' => lang('plugin_path_invalid'),
                'time' => time(),
            ];
        }

        return $resolved . DIRECTORY_SEPARATOR;
    }
}