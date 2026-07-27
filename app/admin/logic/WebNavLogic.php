<?php
namespace app\admin\logic;

use app\admin\model\WebNavModel;

/**
 * 网站导航逻辑
 * @desc 网站导航逻辑
 * @uses \app\admin\logic\WebNavLogic
 */
class WebNavLogic
{
    /**
     * 获取网站导航列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @param int params.page - 页码
     * @param int params.limit - 每页条数
     * @return array
     * @return array list - 导航列表
     * @return int count - 总数量
     */
    public function GetWebNavList(array $params): array
    {
        $WebNavModel = new WebNavModel();

        $query = $WebNavModel;

        $count = (clone $query)->count();
        $list = $query
            ->order('order', 'asc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 50)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建网站导航
     * @author zhaoyj
     * @version v1
     * @param array $params - 导航参数
     * @param string params.name - 导航名称
     * @param string params.url - 导航链接
     * @param string params.target - 打开方式，_self或_blank
     * @param int params.parent_id - 父级id
     * @param int params.order - 排序
     * @param int params.status - 状态
     * @return int - 导航自增id
     */
    public function CreateWebNav(array $params)
    {
        $WebNavModel = new WebNavModel();

        $url = $params['url'] ?? '';
        $result = $this->validateUrlScheme($url);
        if (is_array($result)) {
            return $result;
        }

        return (int)$WebNavModel->insertGetId([
            'name' => $params['name'],
            'url' => $url,
            'target' => $params['target'] ?? '_self',
            'parent_id' => $params['parent_id'] ?? 0,
            'order' => $params['order'] ?? 0,
            'status' => $params['status'] ?? 1,
        ]);
    }

    /**
     * 修改网站导航
     * @author zhaoyj
     * @version v1
     * @param int $id - 导航id
     * @param array $params - 导航参数
     * @param string params.name - 导航名称
     * @param string params.url - 导航链接
     * @param string params.target - 打开方式
     * @param int params.parent_id - 父级id
     * @param int params.order - 排序
     * @param int params.status - 状态
     * @return array|null
     */
    public function UpdateWebNav(int $id, array $params)
    {
        $WebNavModel = new WebNavModel();

        $nav = $WebNavModel->where('id', $id)->find();
        if (!$nav) {
            return [
                'status' => 404,
                'messages' => lang('nav_not_found'),
                'time' => time(),
            ];
        }

        if (isset($params['url'])) {
            $result = $this->validateUrlScheme($params['url']);
            if (is_array($result)) {
                return $result;
            }
        }

        $allowFields = ['name', 'url', 'target', 'parent_id', 'order', 'status'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if ($updateData) {
            $WebNavModel->where('id', $id)->update($updateData);
        }
    }

    /**
     * 删除网站导航
     * @author zhaoyj
     * @version v1
     * @param int $id - 导航id
     * @return array|null
     */
    public function DeleteWebNav(int $id)
    {
        $WebNavModel = new WebNavModel();

        $children = $WebNavModel->where('parent_id', $id)->count();
        if ($children > 0) {
            return [
                'status' => 400,
                'messages' => lang('nav_has_children'),
                'time' => time(),
            ];
        }
        $WebNavModel->where('id', $id)->delete();
    }

    /**
     * 验证URL协议安全性
     * @author zhaoyj
     * @version v1
     * @param string $url - 待验证的URL
     * @return array|null
     */
    protected function validateUrlScheme(string $url)
    {
        if ($url === '') {
            return;
        }
        $url = trim($url);
        if (
            str_starts_with($url, 'http://') ||
            str_starts_with($url, 'https://') ||
            str_starts_with($url, '/') ||
            str_starts_with($url, '#')
        ) {
            return;
        }
        return [
            'status' => 400,
            'messages' => 'URL scheme not allowed, only http://, https://, / and # are permitted',
            'time' => time(),
        ];
    }
}