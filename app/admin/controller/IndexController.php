<?php
namespace app\admin\controller;
use think\facade\View;

/**
 * 主页控制器
 * @desc 主页控制器
 * @author zhaoyj
 * @uses \app\admin\controller\IndexController
 */
class IndexController extends AdminBaseController
{
    public function initialize()
    {
        parent::initialize();
    }
    public function NotFound()
    {
        $result = [
            'status' => 404,
            'messages' => lang('route_not_found'),
            'time' => time(),
        ];
        return json($result,404);
    }
    /**
     * @title Index
     * @desc Index
     * @author zhaoyj
     * @url /
     * @version v1
     * @method get
     * @return View
     */
    public function Index()
    {
        $TemplateData = [
            'SystemFullName' => APP_FULL_VERSION,
            'AppName' => app('http')->getName(),
        ];
        return View::fetch('index',$TemplateData);
    }
}
