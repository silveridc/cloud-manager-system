<?php

namespace app\home\controller;

use app\common\logic\WidgetLogic;
use think\facade\View;
/**
 * 首页控制器
 * @desc 首页控制器
 * @uses \app\home\controller\IndexController
 */
class IndexController extends HomeBaseController
{
    protected WidgetLogic $widgetLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->widgetLogic = app(WidgetLogic::class);
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
            'ICP' => ''
        ];
        return View::fetch('index',$TemplateData);
    }
}