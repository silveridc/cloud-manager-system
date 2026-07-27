<?php

namespace app\home\controller;

use app\common\logic\CommonLogic;

/**
 * 公共控制器
 * @desc 公共控制器
 * @uses \app\home\controller\CommonController
 */
class CommonController extends HomeBaseController
{
    protected CommonLogic $commonLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->commonLogic = app(CommonLogic::class);
    }

    /**
     * @title Country
     * @desc Country
     * @author zhaoyj
     * @version v1
     * @url /console/v1/common/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function country()
    {
        $data = $this->commonLogic->getCountryList();
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }
}