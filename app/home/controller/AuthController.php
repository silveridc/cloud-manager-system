<?php

namespace app\home\controller;

use app\home\logic\ClientAuthLogic;

/**
 * 客户端认证控制器
 * @desc 客户端认证控制器
 * @uses \app\home\controller\AuthController
 */
class AuthController extends HomeBaseController
{
    protected ClientAuthLogic $clientAuthLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->clientAuthLogic = app(ClientAuthLogic::class);
    }

    /**
     * @title 客户登录
     * @desc 客户登录
     * @author zhaoyj
     * @version v1
     * @url /console/v1/auth/login
     * @method post
     * @param string account - 账号(邮箱/手机/用户名) required
     * @param string password - 密码 required
     * @param int remember_password - 记住密码(0否1是)
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return string data.jwt - token
     * @return int data.id - 客户ID
     * @return string data.name - 客户名称
     * @return int time - 时间戳
     */
    public function login()
    {
        $params = $this->request->param();
        $this->validate($params, 'app\common\validate\AuthValidate.client_login');

        $data = $this->clientAuthLogic->login(
            $params['account'],
            $params['password'],
            !empty($params['remember_password'])
        );
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);

        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 客户注册
     * @desc 客户注册
     * @author zhaoyj
     * @version v1
     * @url /console/v1/auth/register
     * @method post
     * @param string username - 用户名
     * @param string email - 邮箱
     * @param string phone - 手机号
     * @param string phone_code - 手机区号
     * @param string password - 密码 required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return string data.jwt - token
     * @return int data.id - 客户ID
     * @return string data.name - 客户名称
     * @return int time - 时间戳
     */
    public function register()
    {
        $params = $this->request->param();
        $this->validate($params, 'app\common\validate\AuthValidate.register');

        $data = $this->clientAuthLogic->register($params);
        if (is_array($data) && isset($data['status']) && $data['status'] !== 200) return json($data, $data['status']);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 客户退出登录
     * @desc 客户退出登录
     * @author zhaoyj
     * @version v1
     * @url /console/v1/auth/logout
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function logout()
    {
        $token = substr($this->request->header('Authorization', ''), 7);
        $this->clientAuthLogic->logout($token);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 修改密码
     * @desc 修改密码
     * @author zhaoyj
     * @version v1
     * @url /console/v1/auth/password
     * @method put
     * @param string old_password - 旧密码 required
     * @param string new_password - 新密码 required
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function changePassword()
    {
        $params = $this->request->param();
        $this->validate($params, 'app\common\validate\AuthValidate.change_password');

        $result = $this->clientAuthLogic->changePassword(
            $this->clientId(),
            $params['old_password'],
            $params['new_password']
        );
        if (isset($result['status'])) return json($result, $result['status']);

        $result = [
            'status' => 200,
            'messages' => lang('password_changed'),
            'time' => time(),
        ];
        return json($result);
    }
}
