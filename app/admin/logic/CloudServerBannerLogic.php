<?php
namespace app\admin\logic;

use app\admin\model\CloudServerBannerModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 云服务器轮播图逻辑类
 * @uses \app\admin\logic\CloudServerBannerLogic
 */
class CloudServerBannerLogic
{
    protected CloudServerBannerModel $model;

    public function __construct(CloudServerBannerModel $model)
    {
        $this->model = $model;
    }

    /**
     * 获取云服务器轮播图列表
     * @author zhaoyj
     * @version v1
     * @return array
     * @return array list - 轮播图列表
     * @return int list.id - 轮播图ID
     * @return string list.img - 图片地址
     * @return string list.url - 跳转链接
     * @return string list.start_time - 开始时间
     * @return string list.end_time - 结束时间
     * @return int list.show - 是否展示(0否1是)
     * @return string list.notes - 备注
     */
    public function GetCloudServerBannerList(): array
    {
        $banners = $this->model
            ->field('id,img,url,start_time,end_time,show,notes')
            ->order('order', 'asc')
            ->select()->toArray();

        return ['list' => $banners];
    }

    /**
     * 添加云服务器轮播图
     * @author zhaoyj
     * @version v1
     * @param array $param .notes - 备注
     * @return int 新增轮播图ID
     */
    public function CreateCloudServerBanner(array $param): int
    {
        $order = $this->model->max('order');

        $banner = new CloudServerBannerModel();
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
     * 修改云服务器轮播图
     * @author zhaoyj
     * @version v1
     * @param int $id - 轮播图ID
     * @param array $param .notes - 备注
     * @return array|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function UpdateCloudServerBanner(int $id, array $param)
    {
        $banner = $this->model->find($id);
        if (!$banner) {
            return [
                'status' => 400,
                'messages' => lang('cloud_server_banner_not_exist'),
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
     * 删除云服务器轮播图
     * @author zhaoyj
     * @version v1
     * @param int $id - 轮播图ID
     * @return array|void
     */
    public function DeleteCloudServerBanner(int $id)
    {
        $banner = $this->model->find($id);
        if (!$banner) {
            return [
                'status' => 400,
                'messages' => lang('cloud_server_banner_not_exist'),
                'time' => time(),
            ];
        }

        $banner->delete();
    }

    /**
     * 切换云服务器轮播图显示状态
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
                'messages' => lang('cloud_server_banner_not_exist'),
                'time' => time(),
            ];
        }

        $banner->save([
            'show' => $show,
            'update_time' => time(),
        ]);
    }

    /**
     * 云服务器轮播图排序
     * @author zhaoyj
     * @version v1
     * @param array $ids - 轮播图ID数组(按顺序)
     * @return array|void
     */
    public function ReorderCloudServerBanner(array $ids)
    {
        // 基础验证
        $existIds = $this->model->column('id');
        if (count($ids) != count($existIds) || count($ids) != count(array_intersect($existIds, $ids))) {
            return [
                'status' => 400,
                'messages' => lang('cloud_server_banner_not_exist'),
                'time' => time(),
            ];
        }

        foreach ($ids as $key => $id) {
            $this->model->where('id', $id)->update(['order' => $key]);
        }
    }
}
