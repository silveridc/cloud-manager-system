<?php
namespace app\admin\logic;

use app\admin\model\IndexBannerModel;

/**
 * 首页轮播图逻辑类
 * @uses \app\admin\logic\IndexBannerLogic
 */
class IndexBannerLogic
{
    protected IndexBannerModel $model;

    public function __construct(IndexBannerModel $model)
    {
        $this->model = $model;
    }

    /**
     * 获取首页轮播图列表
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function GetIndexBannerList(): array
    {
        $banners = $this->model
            ->field('id,img,url,start_time,end_time,show,notes')
            ->order('order', 'asc')
            ->select()->toArray();

        return ['list' => $banners];
    }

    /**
     * 添加首页轮播图
     * @author zhaoyj
     * @version v1
     * @param array $param - 轮播图参数
     * @return int - 轮播图ID
     */
    public function CreateIndexBanner(array $param): int
    {
        $order = $this->model->max('order');

        $banner = new IndexBannerModel();
        $banner->save([
            'img' => $param['img'],
            'url' => $param['url'],
            'start_time' => $param['start_time'],
            'end_time' => $param['end_time'],
            'show' => $param['show'],
            'notes' => $param['notes'] ?? '',
            'order' => $order + 1,
            'create_time' => time(),
        ]);

        return (int)$banner->id;
    }

    /**
     * 修改首页轮播图
     * @author zhaoyj
     * @version v1
     * @param int $id - 轮播图ID
     * @param array $param - 轮播图参数
     * @return array|void
     */
    public function UpdateIndexBanner(int $id, array $param)
    {
        $banner = $this->model->find($id);
        if (!$banner) {
            return [
                'status' => 400,
                'messages' => lang('index_banner_not_exist'),
                'time' => time(),
            ];
        }

        $banner->save([
            'img' => $param['img'],
            'url' => $param['url'],
            'start_time' => $param['start_time'],
            'end_time' => $param['end_time'],
            'show' => $param['show'],
            'notes' => $param['notes'] ?? '',
            'update_time' => time(),
        ]);
    }

    /**
     * 删除首页轮播图
     * @author zhaoyj
     * @version v1
     * @param int $id - 轮播图ID
     * @return array|void
     */
    public function DeleteIndexBanner(int $id)
    {
        $banner = $this->model->find($id);
        if (!$banner) {
            return [
                'status' => 400,
                'messages' => lang('index_banner_not_exist'),
                'time' => time(),
            ];
        }

        $banner->delete();
    }

    /**
     * 展示/隐藏首页轮播图
     * @author zhaoyj
     * @version v1
     * @param int $id - 轮播图ID
     * @param int $show - 是否展示(0否1是)
     * @return array|void
     */
    public function toggleShow(int $id, int $show)
    {
        $banner = $this->model->find($id);
        if (!$banner) {
            return [
                'status' => 400,
                'messages' => lang('index_banner_not_exist'),
                'time' => time(),
            ];
        }

        $banner->save([
            'show' => $show,
            'update_time' => time(),
        ]);
    }

    /**
     * 首页轮播图排序
     * @author zhaoyj
     * @version v1
     * @param array $ids - 轮播图ID数组(按顺序)
     * @return array|void
     */
    public function ReorderIndexBanner(array $ids)
    {
        // 基础验证
        $existIds = $this->model->column('id');
        if (count($ids) != count($existIds) || count($ids) != count(array_intersect($existIds, $ids))) {
            return [
                'status' => 400,
                'messages' => lang('index_banner_not_exist'),
                'time' => time(),
            ];
        }

        foreach ($ids as $key => $id) {
            $this->model->where('id', $id)->update(['order' => $key]);
        }
    }
}
