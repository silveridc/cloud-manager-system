<?php
namespace app\admin\controller;
use app\admin\logic\AdminLogic;
use app\admin\validate\AdminValidate;
/**
 * @title 管理员后台控制器
 * @desc 管理员后台控制器
 * @uses \app\admin\controller\AdminController
 */
class AdminController extends AdminBaseController
{
    protected AdminLogic $AdminLogic;
    protected AdminValidate $AdminValidate;
    protected function initialize()
    {
        parent::initialize();
        $this->AdminLogic = app(AdminLogic::class);
        $this->AdminValidate = app(AdminValidate::class);
    }
    /**
     * @title 获取管理员列表
     * @desc 获取管理员列表
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin
     * @method get
     * @param string keywords - 查找内容
     * @param int status - 管理员状态
     * @param string sort - 排序规则，asc升序，desc降序
     * @param int page - 页码
     * @param int limit - 每页返回的数据条数
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息，多语言
     * @return array data - \app\admin\logic\AdminLogic::GetAdminList
     * @return int time - 时间戳
     * @throws \think\db\exception\DbException
     */
    public function GetAdminList()
    {
        $param = array_merge($this->request->param(), $this->MergeParams());
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $this->AdminLogic->GetAdminList($param),
            'time' => time(),
        ];
        return json($result);
    }
    /**
     * @title 获取管理员信息
     * @desc 获取管理员信息
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/:id
     * @method get
     * @param int $id - 管理员id
     * @return json
     * @return int status - 状态码
     * @return string messages - 提示信息
     * @return array data - \app\admin\logic\AdminLogic::GetAdminInfo
     * @return int time - 时间戳
     */
    public function GetAdminInfo(int $id)
    {
        $data = $this->AdminLogic->GetAdminInfo($id);
        if ($data === null) {
            $result = [
                'status' => 400,
                'messages' => lang('fail_message'),
                'time' => time(),
            ];
            return json($result,400);
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
     * @title 创建管理员
     * @desc 创建管理员
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin
     * @method post
     * @return json
     * @return int status - 状态码
     * @return string messages - 提示信息
     * @return int admin_id - 管理员自增id
     * @remark 普通管理员创建账号时只能分配可委派、低层级且权限为自身权限子集的角色
     * @return int time - 时间戳
     * @throws \Exception
     */
    public function CreateAdmin()
    {
        $param = $this->request->param();
        // 参数验证
        if (!$this->AdminValidate->scene('create')->check($param)){
            return json(['status' => 403 , 'msg' => lang($this->AdminValidate->getError())],403);
        }
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'admin_id' => $this->AdminLogic->CreateAdmin($this->adminId(), $param),
            'time' => time(),
        ];
        return json($result);
    }
    /**
     * @title 修改管理员
     * @desc 修改管理员
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/:id
     * @param int $id - 管理员id
     * @param string name - 管理员用户名
     * @param string email - 管理员邮箱
     * @param string phone - 手机号
     * @param int status - 管理员状态,1正常0禁用
     * @param string password - 修改密码时的新密码
     * @param array role_ids - 角色id数组；普通管理员仅可分配可委派、低于自身最高层级且权限不超出自身的角色 example:[1,2,3]
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     * @noinspection PhpDocSignatureInspection
     */
    public function UpdateAdmin(int $id)
    {
        $this->AdminLogic->UpdateAdmin($this->adminId(), $id, $this->request->param());
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }
    /**
     * @title 删除管理员
     * @desc 删除管理员
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/:id
     * @param int $id
     * @method delete
     * @return json
     * @throws \Throwable
     */
    public function DeleteAdmin(int $id)
    {
        $this->AdminLogic->DeleteAdmin($this->adminId(), $id);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}