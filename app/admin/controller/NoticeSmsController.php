<?php
namespace app\admin\controller;

use app\admin\logic\SmsLogic;

/**
 * 短信通知控制器
 * @desc 短信通知控制器
 * @uses \app\admin\controller\NoticeSmsController
 */
class NoticeSmsController extends AdminBaseController
{
    protected SmsLogic $smsLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->smsLogic = app(SmsLogic::class);
    }

    /**
     * @title 获取短信模板列表
     * @desc 获取短信模板列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/notice_sms
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetSmsTemplateList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->smsLogic->getTemplateList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取短信模板详情
     * @desc 获取短信模板详情
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/notice_sms/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetSmsTemplateInfo(int $id)
    {
        $data = $this->smsLogic->GetSmsTemplateInfo($id);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('template_not_found'),
                'time' => time(),
            ];
            return json($result, 404);
        }
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 修改短信模板
     * @desc 修改短信模板
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/notice_sms/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateSmsTemplate(int $id)
    {
        $params = $this->request->param();
        $result = $this->smsLogic->updateTemplate($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}