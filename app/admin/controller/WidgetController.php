<?php
namespace app\admin\controller;

use app\common\logic\WidgetLogic;

/**
 * 小组件控制器
 * @desc 小组件控制器
 * @uses \app\admin\controller\WidgetController
 */
class WidgetController extends AdminBaseController
{
    protected WidgetLogic $widgetLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->widgetLogic = app(WidgetLogic::class);
    }

    /**
     * @title 获取小组件列表
     * @desc 获取小组件列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/widget
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetWidgetList()
    {
        $data = $this->widgetLogic->getAdminWidgets($this->adminId());
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Update Order
     * @desc Update Order
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/widget/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateWidgetOrder()
    {
        $widgets = $this->request->param('widgets', []);
        $this->widgetLogic->updateOrder($this->adminId(), $widgets);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}