<?php
namespace app\admin\controller;

use app\admin\logic\NoticeSettingLogic;

/**
 * 通知设置控制器
 * @desc 通知设置控制器
 * @uses \app\admin\controller\NoticeSettingController
 */
class NoticeSettingController extends AdminBaseController
{
    protected NoticeSettingLogic $noticeSettingLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->noticeSettingLogic = app(NoticeSettingLogic::class);
    }

    /**
     * @title 获取通知设置列表
     * @desc 获取通知设置列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/notice_setting
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetNoticeSettingList()
    {
        $data = $this->noticeSettingLogic->GetNoticeSettingList();
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 修改通知设置
     * @desc 修改通知设置
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/notice_setting/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateNoticeSetting(int $id)
    {
        $params = $this->request->param();
        $result = $this->noticeSettingLogic->UpdateNoticeSetting($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}