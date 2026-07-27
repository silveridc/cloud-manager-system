<?php
namespace app\admin\logic;

use app\admin\model\MenuModel;

/**
 * 菜单逻辑
 * @desc 菜单逻辑
 * @uses \app\admin\logic\MenuLogic
 */
class MenuLogic
{
    /**
     * 获取后台管理菜单列表
     * @author zhaoyj
     * @version v1
     * @return array 后台菜单列表，按order字段升序排列
     * @return array [].id - 菜单ID
     * @return array [].name - 菜单名称
     * @return array [].icon - 菜单图标
     * @return array [].url - 菜单链接地址
     * @return array [].order - 排序值
     * @return array [].parent_id - 父级菜单ID
     * @return array [].status - 菜单状态
     */
    public function getAdminMenu(): array
    {
        $MenuModel = new MenuModel();
        return $MenuModel
            ->where('type', 'admin')
            ->order('order', 'asc')
            ->select()->toArray();
    }

    /**
     * 获取前台菜单列表
     * @author zhaoyj
     * @version v1
     * @return array 前台菜单列表，按order字段升序排列
     */
    public function getHomeMenu(): array
    {
        $MenuModel = new MenuModel();
        return $MenuModel
            ->where('type', 'home')
            ->order('order', 'asc')
            ->select()->toArray();
    }

    /**
     * 更新菜单
     * @author zhaoyj
     * @version v1
     * @param int $id - 菜单ID
     * @param array $params - 更新参数（name, icon, url, order, parent_id, status）
     * @return array|void
     */
    public function update(int $id, array $params)
    {
        $MenuModel = new MenuModel();
        $menu = $MenuModel->where('id', $id)->find();
        if (!$menu) {
            return [
                'status' => 404,
                'messages' => lang('menu_not_found'),
                'time' => time(),
            ];
        }

        $allowFields = ['name', 'icon', 'url', 'order', 'parent_id', 'status'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if ($updateData) {
            $MenuModel->where('id', $id)->update($updateData);
        }
    }

    /**
     * 创建菜单
     * @author zhaoyj
     * @version v1
     * @param array $params - 菜单参数（type, name, icon, url, order, parent_id, status）
     * @return int 新创建的菜单ID
     */
    public function create(array $params): int
    {
        $MenuModel = new MenuModel();
        return (int)$MenuModel->insertGetId([
            'type' => $params['type'] ?? 'admin',
            'name' => $params['name'],
            'icon' => $params['icon'] ?? '',
            'url' => $params['url'] ?? '',
            'order' => $params['order'] ?? 0,
            'parent_id' => $params['parent_id'] ?? 0,
            'status' => $params['status'] ?? 1,
        ]);
    }

    /**
     * 删除菜单
     * @author zhaoyj
     * @version v1
     * @param int $id - 菜单ID
     * @return array|void
     */
    public function delete(int $id)
    {
        $MenuModel = new MenuModel();
        $children = $MenuModel->where('parent_id', $id)->count();
        if ($children > 0) {
            return [
                'status' => 400,
                'messages' => lang('menu_has_children'),
                'time' => time(),
            ];
        }
        $MenuModel->where('id', $id)->delete();
    }
}