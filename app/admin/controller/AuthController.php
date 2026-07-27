<?php
namespace app\admin\controller;

use app\admin\logic\AdminAuthLogic;
use app\admin\validate\AuthValidate;
use app\common\service\JwtService;

/**
 * @title 管理员鉴权控制器
 * @desc 管理员鉴权控制器
 * @uses \app\admin\controller\AuthController
 */
class AuthController extends AdminBaseController
{
    protected AdminAuthLogic $AdminAuthLogic;
    protected AuthValidate $AuthValidate;

    protected function initialize()
    {
        parent::initialize();
        $this->AdminAuthLogic = app(AdminAuthLogic::class);
        $this->AuthValidate = app(AuthValidate::class);
    }

    /**
     * @title 管理员登录
     * @desc 管理员登录
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/login
     * @param string username - 管理员用户名
     * @param string password - 管理员密码
     * @param bool remember_password - 管理员记住密码选项
     * @return json
     * @return int status - 状态码
     * @return string messages - 提示信息
     * @return array data - 见\app\admin\logic\AdminAuthLogic::AdminLogin
     * @return int time - 时间戳
     * @throws \think\exception\ValidateException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function AdminLogin()
    {
        $param = $this->request->param();
        // 参数验证
        if (!$this->AuthValidate->scene('admin_login')->check($param)){
            return json(['status' => 403 , 'msg' => lang($this->AuthValidate->getError())],403);
        }
        $result = $this->AdminAuthLogic->AdminLogin($param['username'],$param['password'],!empty($param['remember_password']));
        return json($result,$result['status']);
    }

    /**
     * @title 管理员退出登录
     * @desc 管理员退出登录
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/logout
     * @param string Authorization - jwt
     * @return json
     * @return int status - 状态码
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function AdminLogout()
    {
        JwtService::revokeAdmin(substr($this->request->header('Authorization', ''), 7));
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 管理员修改密码
     * @desc 管理员修改密码
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/changepassword
     * @param string oldpass - 管理员原密码
     * @param string newpass - 管理员新密码
     * @return json
     * @return int status - 状态码
     * @return string messages - 提示信息
     * @return int time - 时间戳
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function AdminChangePassword()
    {
        $params = $this->request->param();
        if (!$this->AuthValidate->scene('change_password')->check($params)){
            return json(['status' => 403 , 'msg' => lang($this->AuthValidate->getError())],403);
        }
        $result = $this->AdminAuthLogic->AdminChangePassword($this->adminId(),$params['oldpass'],$params['newpass']);
        return json($result,$result['status']);
    }
}