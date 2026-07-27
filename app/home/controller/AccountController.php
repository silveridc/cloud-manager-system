<?php

namespace app\home\controller;

use app\home\logic\ClientLogic;

/**
 * 账户控制器
 * @desc 账户控制器
 * @uses \app\home\controller\AccountController
 */
class AccountController extends HomeBaseController
{
    protected ClientLogic $clientLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->clientLogic = app(ClientLogic::class);
    }

    /**
     * @title Profile
     * @desc Profile
     * @author zhaoyj
     * @version v1
     * @url /console/v1/account/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function profile()
    {
        $data = $this->clientLogic->getProfile($this->clientId());
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        if (!$data) {
            $result = [
                'status' => 404,
                'messages' => lang('client_not_found'),
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
     * @title 修改个人信息
     * @desc 修改个人信息
     * @author zhaoyj
     * @version v1
     * @url /console/v1/account/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function UpdateProfile()
    {
        $params = $this->request->param();
        $this->validate($params, 'app\common\validate\ClientValidate.client_update');
        $result = $this->clientLogic->updateProfile($this->clientId(), $params);
        if (isset($result['status'])) return json($result, $result['status']);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
}
