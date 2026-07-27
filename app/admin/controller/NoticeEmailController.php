<?php
namespace app\admin\controller;

use app\admin\logic\EmailLogic;

/**
 * 邮件通知控制器
 * @desc 邮件通知控制器
 * @uses \app\admin\controller\NoticeEmailController
 */
class NoticeEmailController extends AdminBaseController
{
    protected EmailLogic $emailLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->emailLogic = app(EmailLogic::class);
    }

    /**
     * @title 获取邮件模板列表
     * @desc 获取邮件模板列表
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/notice_email
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetEmailTemplateList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->emailLogic->getTemplateList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取邮件模板详情
     * @desc 获取邮件模板详情
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/notice_email/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function GetEmailTemplateInfo(int $id)
    {
        $data = $this->emailLogic->GetEmailTemplateInfo($id);
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
     * @title 修改邮件模板
     * @desc 修改邮件模板
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/notice_email/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateEmailTemplate(int $id)
    {
        $params = $this->request->param();
        $result = $this->emailLogic->updateTemplate($id, $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}