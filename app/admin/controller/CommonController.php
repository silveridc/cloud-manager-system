<?php
namespace app\admin\controller;

use app\common\logic\CommonLogic;
use app\admin\logic\FeedbackLogic;
use app\common\logic\UploadService;

/**
 * 公共控制器
 * @desc 公共控制器
 * @uses \app\admin\controller\CommonController
 */
class CommonController extends AdminBaseController
{
    protected CommonLogic $commonLogic;
    protected UploadService $uploadService;
    protected FeedbackLogic $feedbackLogic;
    protected function initialize()
    {
        parent::initialize();
        $this->commonLogic = app(CommonLogic::class);
        $this->uploadService = app(UploadService::class);
        $this->feedbackLogic = app(FeedbackLogic::class);
    }

    /**
     * @title 上传文件
     * @desc 上传文件
     * @author zhaoyj
     * @return \think\response\Json
     */
    public function upload()
    {
        $file = $this->request->file('file');
        if (!$file) {
            $result = [
                'status' => 400,
                'messages' => lang('file_required'),
                'time' => time(),
            ];
            return json($result, 400);
        }
        $dir = $this->request->param('dir', 'common');
        $data = $this->uploadService->upload($file, $dir);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Global Search
     * @desc Global Search
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/search
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function globalSearch()
    {
        $keywords = $this->request->param('keywords', '');
        $data = $this->commonLogic->globalSearch($keywords, $this->adminId());
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Feedback List
     * @desc Feedback List
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/feedback
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function feedbackList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $data = $this->feedbackLogic->getList($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Feedback Type List
     * @desc Feedback Type List
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/feedback_type
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function feedbackTypeList()
    {
        $data = $this->feedbackLogic->getTypeList();
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }
}