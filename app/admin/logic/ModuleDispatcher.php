<?php
namespace app\admin\logic;

use app\common\model\HostModel;
use app\common\model\ProductModel;
use app\common\model\ServerModel;

/**
 * 模块调度器
 * @desc 模块调度器
 * @uses \app\admin\logic\ModuleDispatcher
 */
class ModuleDispatcher
{
    protected PluginManager $pluginManager;

    public function __construct(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * 调用模块创建账号
     * @author zhaoyj
     * @version v1
     * @param int $hostId - 产品实例ID
     * @return array
     */
    public function createAccount(int $hostId): array
    {
        $hostModel = new HostModel();
        $productModel = new ProductModel();
        $serverModel = new ServerModel();

        $host = $hostModel->where('id', $hostId)->find();
        if (!$host) {
            return ['status' => 400, 'messages' => 'host not found'];
        }

        $product = $productModel->where('id', $host['product_id'])->find();
        $server = $host['server_id'] ? $serverModel->where('id', $host['server_id'])->find() : null;

        $module = $product['type'] ?? '';
        if (!$module) {
            return ['status' => 400, 'messages' => 'no module configured'];
        }

        return $this->callModule($module, 'createAccount', [
            'host' => $host,
            'product' => $product,
            'server' => $server,
        ]);
    }

    /**
     * 调用模块暂停账号
     * @author zhaoyj
     * @version v1
     * @param int $hostId - 产品实例ID
     * @return array
     */
    public function suspendAccount(int $hostId): array
    {
        return $this->callHostModule($hostId, 'suspendAccount');
    }

    /**
     * 调用模块解除暂停
     * @author zhaoyj
     * @version v1
     * @param int $hostId - 产品实例ID
     * @return array
     */
    public function unsuspendAccount(int $hostId): array
    {
        return $this->callHostModule($hostId, 'unsuspendAccount');
    }

    /**
     * 调用模块终止账号
     * @author zhaoyj
     * @version v1
     * @param int $hostId - 产品实例ID
     * @return array
     */
    public function terminateAccount(int $hostId): array
    {
        return $this->callHostModule($hostId, 'terminateAccount');
    }

    /**
     * 获取模块客户端区域内容
     * @author zhaoyj
     * @version v1
     * @param int $hostId - 产品实例ID
     * @return array
     */
    public function clientArea(int $hostId): array
    {
        return $this->callHostModule($hostId, 'clientArea');
    }

    /**
     * 通用调用产品实例关联的模块方法
     */
    private function callHostModule(int $hostId, string $method): array
    {
        $hostModel = new HostModel();
        $productModel = new ProductModel();
        $serverModel = new ServerModel();

        $host = $hostModel->where('id', $hostId)->find();
        if (!$host) {
            return ['status' => 400, 'messages' => 'host not found'];
        }

        $product = $productModel->where('id', $host['product_id'])->find();
        $server = $host['server_id'] ? $serverModel->where('id', $host['server_id'])->find() : null;

        $module = $product['type'] ?? '';
        if (!$module) {
            return ['status' => 400, 'messages' => 'no module configured'];
        }

        return $this->callModule($module, $method, [
            'host' => $host,
            'product' => $product,
            'server' => $server,
        ]);
    }

    /**
     * 调用服务器模块插件
     */
    private function callModule(string $module, string $method, array $params): array
    {
        try {
            $result = $this->pluginManager->call('server', $module, $method, $params);
            return is_array($result) ? $result : ['status' => 200, 'messages' => 'success', 'data' => $result];
        } catch (\Throwable $e) {
            return ['status' => 400, 'messages' => $e->getMessage()];
        }
    }
}