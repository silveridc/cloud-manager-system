<?php
namespace app\common\logic;

use app\common\model\CountryModel;
use app\common\model\ClientModel;
use app\common\model\OrderModel;
use app\common\model\HostModel;

/**
 * 公共逻辑
 * @desc 公共逻辑
 * @uses \app\common\logic\CommonLogic
 */
class CommonLogic
{
    /**
     * 获取国家列表
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function getCountryList(): array
    {
        $CountryModel = new CountryModel();

        return $CountryModel
            ->field('id, name_zh, name_en, phone_code, iso, iso3')
            ->order('order', 'asc')
            ->select()->toArray();
    }

    /**
     * 全局搜索
     * @author zhaoyj
     * @version v1
     * @param string $keywords - 搜索关键词
     * @param int $adminId - 管理员ID
     * @return array
     */
    public function globalSearch(string $keywords, int $adminId = 0): array
    {
        $ClientModel = new ClientModel();
        $OrderModel = new OrderModel();
        $HostModel = new HostModel();

        $result = [];

        if (strlen($keywords) < 2) {
            return $result;
        }

        $kw = '%' . $keywords . '%';

        // 搜客户
        $clients = $ClientModel
            ->where('username|email|phone', 'like', $kw)
            ->field('id, username, email')
            ->limit(5)
            ->select()
            ->toArray();
        if ($clients) {
            $result['clients'] = $clients;
        }

        // 搜订单
        $orders = $OrderModel
            ->where('id', 'like', $kw)
            ->field('id, status, amount')
            ->limit(5)->select()->toArray();
        if ($orders) {
            $result['orders'] = $orders;
        }

        // 搜产品实例
        $hosts = $HostModel
            ->where('name', 'like', $kw)
            ->where('status', '<>', 'Deleted')
            ->field('id, name, status')
            ->limit(5)
            ->select()
            ->toArray();
        if ($hosts) {
            $result['hosts'] = $hosts;
        }

        return $result;
    }
}