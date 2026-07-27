<?php
namespace app\admin\logic;

use app\admin\model\FriendlyLinkModel;
use app\admin\model\HonorModel;
use app\admin\model\PartnerModel;
use app\admin\model\BottomBarGroupModel;
use app\admin\model\BottomBarNavModel;
use app\admin\model\SideFloatingWindowModel;

/**
 * 内容管理逻辑类
 * @uses \app\admin\logic\ContentLogic
 */
class ContentLogic
{
    // ==================== 友情链接 ====================

    /**
     * 获取友情链接列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数（page, limit）
     * @return array 包含list和count的数组
     */
    public function getFriendlyLinks(array $params): array
    {
        $friendlyLinkModel = new FriendlyLinkModel();
        $count = (clone $friendlyLinkModel)->count();
        $list = $friendlyLinkModel->order('order', 'asc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();
        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建友情链接
     * @author zhaoyj
     * @version v1
     * @param array $params - 友情链接参数（name, url, logo, order）
     * @return int 新创建的友情链接ID
     */
    public function createFriendlyLink(array $params): int
    {
        $friendlyLinkModel = new FriendlyLinkModel();
        return (int)$friendlyLinkModel->insertGetId([
            'name' => $params['name'],
            'url' => $params['url'] ?? '',
            'logo' => $params['logo'] ?? '',
            'order' => $params['order'] ?? 0,
        ]);
    }

    /**
     * 更新友情链接
     * @author zhaoyj
     * @version v1
     * @param int $id - 友情链接ID
     * @param array $params - 更新参数（name, url, order, status）
     * @return void
     */
    public function updateFriendlyLink(int $id, array $params): void
    {
        $friendlyLinkModel = new FriendlyLinkModel();
        $allowFields = ['name', 'url', 'order', 'status'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }
        if (!empty($updateData)) {
            $friendlyLinkModel->where('id', $id)->update($updateData);
        }
    }

    /**
     * 删除友情链接
     * @author zhaoyj
     * @version v1
     * @param int $id - 友情链接ID
     * @return void
     */
    public function deleteFriendlyLink(int $id): void
    {
        $friendlyLinkModel = new FriendlyLinkModel();
        $friendlyLinkModel->where('id', $id)->delete();
    }

    // ==================== 荣誉 ====================

    /**
     * 获取荣誉列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数（page, limit）
     * @return array 包含list和count的数组
     */
    public function getHonors(array $params): array
    {
        $honorModel = new HonorModel();
        $count = (clone $honorModel)->count();
        $list = $honorModel->order('order', 'asc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();
        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建荣誉
     * @author zhaoyj
     * @version v1
     * @param array $params - 荣誉参数
     * @return int - 荣誉ID
     */
    public function createHonor(array $params): int
    {
        $honorModel = new HonorModel();
        return (int)$honorModel->insertGetId([
            'name' => $params['name'] ?? '',
            'img' => $params['img'] ?? '',
            'order' => $params['order'] ?? 0,
        ]);
    }

    /**
     * 更新荣誉
     * @author zhaoyj
     * @version v1
     * @param int $id - 荣誉ID
     * @param array $params - 更新参数
     * @return void
     */
    public function updateHonor(int $id, array $params): void
    {
        $honorModel = new HonorModel();
        $allowFields = ['name', 'img', 'order', 'status'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }
        if (!empty($updateData)) {
            $honorModel->where('id', $id)->update($updateData);
        }
    }

    /**
     * 删除荣誉
     * @author zhaoyj
     * @version v1
     * @param int $id - 荣誉ID
     * @return void
     */
    public function deleteHonor(int $id): void
    {
        $honorModel = new HonorModel();
        $honorModel->where('id', $id)->delete();
    }

    // ==================== 合作伙伴 ====================

    /**
     * 获取合作伙伴列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function getPartners(array $params): array
    {
        $partnerModel = new PartnerModel();
        $count = (clone $partnerModel)->count();
        $list = $partnerModel->order('order', 'asc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();
        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建合作伙伴
     * @author zhaoyj
     * @version v1
     * @param array $params - 合作伙伴参数
     * @return int - 合作伙伴ID
     */
    public function createPartner(array $params): int
    {
        $partnerModel = new PartnerModel();
        return (int)$partnerModel->insertGetId([
            'name' => $params['name'] ?? '',
            'url' => $params['url'] ?? '',
            'logo' => $params['logo'] ?? '',
            'order' => $params['order'] ?? 0,
        ]);
    }

    /**
     * 更新合作伙伴
     * @author zhaoyj
     * @version v1
     * @param int $id - 合作伙伴ID
     * @param array $params - 更新参数
     * @return void
     */
    public function updatePartner(int $id, array $params): void
    {
        $partnerModel = new PartnerModel();
        $allowFields = ['name', 'logo', 'url', 'order', 'status'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }
        if (!empty($updateData)) {
            $partnerModel->where('id', $id)->update($updateData);
        }
    }

    /**
     * 删除合作伙伴
     * @author zhaoyj
     * @version v1
     * @param int $id - 合作伙伴ID
     * @return void
     */
    public function deletePartner(int $id): void
    {
        $partnerModel = new PartnerModel();
        $partnerModel->where('id', $id)->delete();
    }

    // ==================== 底部栏 ====================

    /**
     * 获取底部栏分组列表
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function getBottomBarGroups(): array
    {
        $bottomBarGroupModel = new BottomBarGroupModel();
        $groups = $bottomBarGroupModel
            ->order('order', 'asc')
            ->select()->toArray();

        if ($groups) {
            $bottomBarNavModel = new BottomBarNavModel();
            $groupIds = array_column($groups, 'id');
            $navs = $bottomBarNavModel
                ->whereIn('group_id', $groupIds)
                ->order('order', 'asc')
                ->select()->toArray();

            $navMap = [];
            foreach ($navs as $nav) {
                $navMap[$nav['group_id']][] = $nav;
            }

            foreach ($groups as &$group) {
                $group['navs'] = $navMap[$group['id']] ?? [];
            }
        }

        return $groups;
    }

    // ==================== 浮窗 ====================

    /**
     * 获取侧边浮窗列表
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function getSideFloatingWindows(): array
    {
        $sideFloatingWindowModel = new SideFloatingWindowModel();
        return $sideFloatingWindowModel
            ->order('order', 'asc')
            ->select()->toArray();
    }

    /**
     * 更新侧边浮窗
     * @author zhaoyj
     * @version v1
     * @param int $id - 浮窗ID
     * @param array $params - 更新参数
     * @return void
     */
    public function updateSideFloatingWindow(int $id, array $params): void
    {
        $sideFloatingWindowModel = new SideFloatingWindowModel();
        $allowFields = ['name', 'url', 'icon', 'image', 'content', 'order', 'status', 'position'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }
        if (!empty($updateData)) {
            $sideFloatingWindowModel->where('id', $id)->update($updateData);
        }
    }
}