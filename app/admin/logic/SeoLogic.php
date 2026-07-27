<?php
namespace app\admin\logic;

use app\admin\model\SeoModel;

/**
 * SEO逻辑
 * @desc SEO逻辑
 * @uses \app\admin\logic\SeoLogic
 */
class SeoLogic
{
    /**
     * 获取SEO列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @param string params.sort - 排序规则
     * @param int params.page - 页码
     * @param int params.limit - 每页条数
     * @return array
     * @return array list - SEO列表
     * @return int count - 总数量
     */
    public function GetSeoList(array $params): array
    {
        $SeoModel = new SeoModel();
        $query = $SeoModel;

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 修改SEO信息
     * @author zhaoyj
     * @version v1
     * @param int $id - SEO记录id
     * @param array $params - SEO参数
     * @param string params.title - 页面标题
     * @param string params.keywords - 关键词
     * @param string params.description - 描述
     * @param string params.page - 页面标识
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function UpdateSeo(int $id, array $params)
    {
        $SeoModel = new SeoModel();
        $seo = $SeoModel->where('id', $id)->find();
        if (!$seo) {
            return [
                'status' => 404,
                'messages' => lang('seo_not_found'),
                'time' => time(),
            ];
        }

        $allowFields = ['title', 'keywords', 'description', 'page'];
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if ($updateData) {
            (new SeoModel())->where('id', $id)->update($updateData);
        }
    }

    /**
     * 创建SEO记录
     * @author zhaoyj
     * @version v1
     * @param array $params - SEO参数
     * @param string params.page - 页面标识
     * @param string params.title - 页面标题
     * @param string params.keywords - 关键词
     * @param string params.description - 描述
     * @return int - SEO记录自增id
     */
    public function CreateSeo(array $params): int
    {
        $SeoModel = new SeoModel();
        return (int)$SeoModel->insertGetId([
            'page' => $params['page'] ?? '',
            'title' => $params['title'] ?? '',
            'keywords' => $params['keywords'] ?? '',
            'description' => $params['description'] ?? '',
        ]);
    }

    /**
     * 删除SEO记录
     * @author zhaoyj
     * @version v1
     * @param int $id - SEO记录id
     * @return void
     */
    public function DeleteSeo(int $id): void
    {
        $SeoModel = new SeoModel();
        $SeoModel->where('id', $id)->delete();
    }
}