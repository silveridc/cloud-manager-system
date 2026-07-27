<?php
namespace app\admin\controller;

use app\admin\logic\WebNavLogic;

/**
 * 网站导航控制器
 * @desc 网站导航控制器
 * @uses \app\admin\controller\WebNavController
 */
class WebNavController extends AdminBaseController
{
    protected WebNavLogic $webNavLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->webNavLogic = app(WebNavLogic::class);
    }

    /**
     * @title 获取网站导航列表
     * @desc 获取网站导航列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/web_nav
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetWebNavList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->webNavLogic->GetWebNavList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 创建网站导航
     * @desc 创建网站导航
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/web_nav
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function CreateWebNav()
    {
        $params = $this->request->param();
        $id = $this->webNavLogic->CreateWebNav($params);
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
     * @title 修改网站导航
     * @desc 修改网站导航
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/web_nav/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateWebNav(int $id)
    {
        $params = $this->request->param();
        $result = $this->webNavLogic->UpdateWebNav($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除网站导航
     * @desc 删除网站导航
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/web_nav/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function DeleteWebNav(int $id)
    {
        $result = $this->webNavLogic->DeleteWebNav($id);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}