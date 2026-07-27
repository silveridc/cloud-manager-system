<?php
namespace app\admin\controller;

use app\admin\logic\SeoLogic;

/**
 * SEO控制器
 * @desc SEO控制器
 * @uses \app\admin\controller\SeoController
 */
class SeoController extends AdminBaseController
{
    protected SeoLogic $seoLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->seoLogic = app(SeoLogic::class);
    }

    /**
     * @title 获取SEO列表
     * @desc 获取SEO列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/seo
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetSeoList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->seoLogic->GetSeoList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 创建SEO
     * @desc 创建SEO
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/seo
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CreateSeo()
    {
        $params = $this->request->param();
        $id = $this->seoLogic->CreateSeo($params);
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
     * @title 修改SEO
     * @desc 修改SEO
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/seo/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateSeo(int $id)
    {
        $params = $this->request->param();
        $result = $this->seoLogic->UpdateSeo($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除SEO
     * @desc 删除SEO
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/seo/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteSeo(int $id)
    {
        $this->seoLogic->DeleteSeo($id);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}