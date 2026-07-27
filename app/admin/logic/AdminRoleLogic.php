<?php
namespace app\admin\logic;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;
use app\admin\model\AdminRoleModel;
use app\admin\model\AdminRuleModel;
use app\admin\model\AdminRoleLinkModel;
/**
 * 管理员权限规则逻辑
 * @author zhaoyj
 * @use \app\admin\logic\AdminRoleLogic
 */
class AdminRoleLogic
{
    /**
     * 分页获取管理员角色列表
     * @author zhaoyj
     * @version v1
     * @param array $params ['limit'] - 每页数量，默认20
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function GetAdminRoleList(array $params): array
    {
        $AdminRoleModel = new AdminRoleModel();
        if (!empty($params['keywords'])) {
            $AdminRoleModel->where('name', 'like', '%' . $params['keywords'] . '%');
        }

        $count = $AdminRoleModel->count();
        $list = $AdminRoleModel->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取单个角色详情信息
     * @author zhaoyj
     * @version v1
     * @param int $id - 角色ID
     * @return array|null
     * @return int id - 角色ID
     * @return string name - 角色名称
     * @return string description - 角色描述
     * @return int create_time - 创建时间
     * @return int update_time - 更新时间
     * @return array rules. - 该角色拥有的权限标识列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function GetAdminRoleInfo(int $id): ?array
    {
        $AdminRoleModel = new AdminRoleModel();
        $role = $AdminRoleModel->where('id', $id)->find();
        if (!$role) {
            return null;
        }
        $AdminRuleModel = new AdminRuleModel();
        $role['rules'] = $AdminRuleModel->where('role_id', $id)->column('name');
        return $role;
    }

    /**
     * 创建管理员规则
     * @author zhaoyj
     * @version v1
     * @param array $params - 参考\app\admin\controller\AdminRoleController::CreateAdminRole下注释
     * @return int id - 规则id
     * @throws \Throwable
     */
    public function CreateAdminRole(array $params): int
    {
        $AdminRoleModel = new AdminRoleModel();
        $AdminRoleModel->startTrans();
        try {
            $id = (int)$AdminRoleModel->insertGetId([
                'name' => $params['name'],
                'description' => $params['description'] ?? '',
                'create_time' => time(),
                'update_time' => time(),
            ]);

            if (!empty($params['rules'])) {
                $rules = [];
                foreach ($params['rules'] as $rule) {
                    $rules[] = ['role_id' => $id, 'name' => $rule];
                }
                $AdminRuleModel = new AdminRuleModel();
                $AdminRuleModel->insertAll($rules);
            }

            $AdminRoleModel->commit();
            return $id;
        } catch (\Throwable $e) {
            $AdminRoleModel->rollback();
            throw $e;
        }
    }

    /**
     * 修改管理员规则
     * @author zhaoyj
     * @version v1
     * @param int $id - 规则id
     * @param array $params - 参考\app\admin\controller\AdminRoleController::UpdateAdminRole
     * @return void
     */
    public function UpdateAdminRole(int $id, array $params): void
    {
        $AdminRoleModel = new AdminRoleModel();
        $role = $AdminRoleModel->where('id', $id)->find();
        if (!$role) {
            throw new \think\exception\ValidateException(lang('role_not_found'));
        }

        $AdminRoleModel->startTrans();
        try {
            $updateData = ['update_time' => time()];
            if (isset($params['name'])) {
                $updateData['name'] = $params['name'];
            }
            if (isset($params['description'])) {
                $updateData['description'] = $params['description'];
            }
            $AdminRoleModel->where('id', $id)->update($updateData);

            if (isset($params['rules'])) {
                $AdminRuleModel = new AdminRuleModel();
                $AdminRuleModel->where('role_id', $id)->delete();
                if (!empty($params['rules'])) {
                    $rules = [];
                    foreach ($params['rules'] as $rule) {
                        $rules[] = ['role_id' => $id, 'name' => $rule];
                    }
                    $AdminRuleModel->insertAll($rules);
                }

                // 清除该角色关联的所有管理员缓存
                $adminIds = (new AdminRoleLinkModel())->where('role_id', $id)
                    ->column('admin_id');
                foreach ($adminIds as $adminId) {
                    Cache::delete('admin_rules:' . $adminId);
                }
            }
            $AdminRoleModel->commit();
        } catch (\Throwable $e) {
            $AdminRoleModel->rollback();
            throw $e;
        }
    }

    /**
     * 删除管理员规则
     * @author zhaoyj
     * @version v1
     * @param int $id - 规则id
     * @return void
     * @throws \Throwable
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function DeleteAdminRole(int $id): void
    {
        $AdminRoleModel = new AdminRoleModel();
        $role = $AdminRoleModel->where('id', $id)->find();
        if (!$role) {
            throw new \think\exception\ValidateException(lang('role_not_found'));
        }

        $linkCount = (new AdminRoleLinkModel())->where('role_id', $id)->count();
        if ($linkCount > 0) {
            throw new \think\exception\ValidateException(lang('role_in_use'));
        }

        $AdminRoleModel->startTrans();
        try {
            $AdminRoleModel->where('id', $id)->delete();
            $AdminRuleModel = new AdminRuleModel();
            $AdminRuleModel->where('role_id', $id)->delete();
            $AdminRoleModel->commit();
        } catch (\Throwable $e) {
            $AdminRoleModel->rollback();
            throw $e;
        }
    }
}