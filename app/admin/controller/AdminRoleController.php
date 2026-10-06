<?php
namespace app\admin\controller;

use app\admin\logic\AdminRoleLogic;

/**
 * @title 管理员规则控制器
 * @desc 管理员规则控制器
 * @uses \app\admin\controller\AdminRoleController
 */
class AdminRoleController extends AdminBaseController
{
    protected AdminRoleLogic $AdminRoleLogic;
    protected function initialize()
    {
        parent::initialize();
        $this->AdminRoleLogic = app(AdminRoleLogic::class);
    }

    /**
     * @title 获取管理员权限规则
     * @desc 获取管理员权限规则
     * @author zhaoyj
     * @version v1
     * @method get
     * @url /{admin}/v1/admin/role
     * @param string keywords - 查找内容
     * @param string sort - 排序规则，asc升序，desc降序
     * @param int page - 页码,默认为1
     * @param int limit - 每页的条数
     * @return \think\response\Json
     * @return int status - 状态码
     * @return string messages - 多语言提示信息
     * @return array data - \app\admin\logic\AdminRoleLogic::GetAdminRoleList
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function GetAdminRoleList()
    {
        $params = array_merge($this->request->param(), $this->MergeParams());
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $this->AdminRoleLogic->GetAdminRoleList($params),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 获取角色信息
     * @desc 获取角色信息
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/role/:id
     * @param int id - 角色id
     * @method get
     * @return json
     * @return int status - 状态码
     * @return string messages - 提示信息
     * @return array data - \app\admin\logic\AdminRoleLogic::GetAdminRoleInfo
     * @return int time - 时间戳
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function GetAdminRoleInfo(int $id)
    {
        $data = $this->AdminRoleLogic->GetAdminRoleInfo($id);
        if ($data === null) {
            $result = [
                'status' => 404,
                'messages' => lang('role_not_found'),
                'time' => time(),
            ];
            return json($result,404);
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
     * @title 创建管理员规则
     * @desc 创建管理员规则
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/role
     * @param string name - 规则名称
     * @param string description - 管理员规则描述
     * @param int level - 角色层级，数值越大层级越高
     * @param int delegable - 是否允许上级管理员委派，0否1是
     * @param array rules. - 权限规则列表
     * @remark 层级数值越大权限层级越高；普通管理员只能操作低于自身最高层级且权限为自身权限子集的可委派角色
     * @return json
     * @return int id - \app\admin\logic\AdminRoleLogic::CreateAdminRole
     * @throws \Throwable
     */
    public function CreateAdminRole()
    {
        $params = $this->request->param();
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $this->AdminRoleLogic->CreateAdminRole($this->adminId(), $params),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 修改管理员规则
     * @desc 修改管理员规则
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/role/:id
     * @method put
     * @param int id - 规则id
     * @param string name - 规则名称
     * @param string description - 规则描述
     * @param int level - 角色层级，数值越大层级越高
     * @param int delegable - 是否允许上级管理员委派，0否1是
     * @param array rules. - 权限规则列表
     * @remark 层级数值越大权限层级越高；普通管理员只能操作低于自身最高层级且权限为自身权限子集的可委派角色
     * @return json
     * @return int status - 状态码
     * @return string messages - 提示信息
     * @return int time - 时间戳
     * @throws \Throwable
     */
    public function UpdateAdminRole(int $id)
    {
        $params = $this->request->param();
        $this->AdminRoleLogic->UpdateAdminRole($this->adminId(), $id, $params);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title 删除管理员规则
     * @desc 删除管理员规则
     * @author zhaoyj
     * @version v1
     * @url /{admin}/v1/admin/role/:id
     * @param int $id - 规则id
     * @return json
     * @return int status - 状态码
     * @return string messages - 提示信息
     * @return int time - 时间戳
     * @throws \Throwable
     */
    public function DeleteAdminRole(int $id)
    {
        $this->AdminRoleLogic->DeleteAdminRole($this->adminId(), $id);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}