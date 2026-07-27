<?php
namespace app\admin\controller;

use app\admin\logic\IndexBannerLogic;
use app\admin\validate\IndexBannerValidate;

/**
 * @title 首页轮播图控制器
 * @desc 首页轮播图控制器
 * @uses \app\admin\controller\IndexBannerController
 */
class IndexBannerController extends AdminBaseController
{
    protected IndexBannerLogic $indexBannerLogic;
    protected IndexBannerValidate $validate;

    protected function initialize()
    {
        parent::initialize();
        $this->indexBannerLogic = app(IndexBannerLogic::class);
        $this->validate = new IndexBannerValidate();
    }

    /**
     * @title 首页轮播图列表
     * @desc 首页轮播图列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/index_banner
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return array data.list - 轮播图列表
     * @return int data.list[].id - 轮播图ID
     * @return string data.list[].img - 图片
     * @return string data.list[].url - 跳转链接
     * @return int data.list[].start_time - 展示开始时间
     * @return int data.list[].end_time - 展示结束时间
     * @return int data.list[].show - 是否展示0否1是
     * @return string data.list[].notes - 备注
     * @return int time - 时间戳
     */
    public function GetIndexBannerList()
    {
        $data = $this->indexBannerLogic->GetIndexBannerList();
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 添加首页轮播图
     * @desc 添加首页轮播图
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/index_banner
     * @method post
     * @param string img - 图片 required
     * @param string url - 跳转链接 required
     * @param int start_time - 展示开始时间 required
     * @param int end_time - 展示结束时间 required
     * @param int show - 是否展示0否1是 required
     * @param string notes - 备注
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int data.id - 轮播图ID
     * @return int time - 时间戳
     */
    public function CreateIndexBanner()
    {
        $param = $this->request->param();

        if (!$this->validate->scene('create')->check($param)) {
            $result = [
                'status' => 400,
                'messages' => lang($this->validate->getError()),
                'time' => time(),
            ];
            return json($result, 400);
        }

        $id = $this->indexBannerLogic->CreateIndexBanner($param);
        if (is_array($id) && isset($id['status'])) return json($id, $id['status']);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $id,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 修改首页轮播图
     * @desc 修改首页轮播图
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/index_banner/:id
     * @method put
     * @param int id - 轮播图ID required
     * @param string img - 图片 required
     * @param string url - 跳转链接 required
     * @param int start_time - 展示开始时间 required
     * @param int end_time - 展示结束时间 required
     * @param int show - 是否展示0否1是 required
     * @param string notes - 备注
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateIndexBanner(int $id)
    {
        $param = $this->request->param();
        $param['id'] = $id;

        if (!$this->validate->scene('update')->check($param)) {
            $result = [
                'status' => 400,
                'messages' => lang($this->validate->getError()),
                'time' => time(),
            ];
            return json($result, 400);
        }

        $result = $this->indexBannerLogic->UpdateIndexBanner($id, $param);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除首页轮播图
     * @desc 删除首页轮播图
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/index_banner/:id
     * @method delete
     * @param int id - 轮播图ID required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteIndexBanner(int $id)
    {
        $result = $this->indexBannerLogic->DeleteIndexBanner($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 展示/隐藏首页轮播图
     * @desc 展示/隐藏首页轮播图
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/index_banner/:id/show
     * @method put
     * @param int id - 轮播图ID required
     * @param int show - 是否展示0否1是 required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function ToggleIndexBannerShow(int $id)
    {
        $param = $this->request->param();
        $param['id'] = $id;

        if (!$this->validate->scene('show')->check($param)) {
            $result = [
                'status' => 400,
                'messages' => lang($this->validate->getError()),
                'time' => time(),
            ];
            return json($result, 400);
        }

        $this->indexBannerLogic->toggleShow($id, (int)$param['show']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 首页轮播图排序
     * @desc 首页轮播图排序
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/index_banner/order
     * @method put
     * @param array id - 轮播图ID数组(按顺序) required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function ReorderIndexBanner()
    {
        $param = $this->request->param();
        $ids = $param['id'] ?? [];

        $result = $this->indexBannerLogic->ReorderIndexBanner($ids);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}
