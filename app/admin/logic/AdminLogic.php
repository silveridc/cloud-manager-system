<?php
namespace app\admin\logic;
use think\facade\Cache;
use app\common\service\JwtService;
use app\admin\model\AdminModel;
use app\admin\model\AdminRoleLinkModel;
use app\admin\service\AdminDelegationService;

/**
 * 管理员后台逻辑类
 * @desc 管理员后台逻辑类
 * @author zhaoyj
 * @use \app\admin\logic\AdminLogic
 */
class AdminLogic
{
    private AdminDelegationService $delegationService;

    public function __construct(AdminDelegationService $delegationService)
    {
        $this->delegationService = $delegationService;
    }

    /**
     * 获取管理员列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 参考\app\admin\controller\AdminController::GetAdminList下注释
     * @return array
     * @return int list.id - 管理员id
     * @return string list.name - 管理员un
     * @return string list.email - 管理员邮件
     * @return string list.phone - 管理员手机
     * @return int list.status - 管理员状态，1正常0禁用
     * @return int list.last_login_time - 管理员上一次登录时间戳
     * @return string list.last_login_ip - 上一次登录ip
     * @return int list.create_time - 账号创建时间
     * @return array list.roles. - 管理员角色列表，未分配返回[]
     * @return int list.roles.id - 管理员角色列表id
     * @return string list.roles.name - 管理员角色列表名称
     * @return int count - 总数据数量
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function GetAdminList(array $params): array
    {
        $AdminModel = new AdminModel();

        // query参数
        $query = $AdminModel->field('id, name, email, phone, status, last_login_time, last_login_ip, create_time');
        //搜索关键字 id,名称,用户名,邮箱
        if (!empty($params['keywords'])) {
            $kw = '%' . $params['keywords'] . '%';
            $query->where('name|email|phone', 'like', $kw);
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }
        $count = (clone $query)->count();
        $list = $query->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();
        // 批量加载角色
        if ($list) {
            $roleLinks = (new AdminRoleLinkModel())->alias('arlk')
                ->leftJoin('admin_role arl', 'arl.id = arlk.role_id')
                ->whereIn('arlk.admin_id', array_column($list, 'id'))
                ->field('arlk.admin_id, arl.id as role_id, arl.name as role_name, arl.level, arl.delegable')
                ->select()
                ->toArray();
            $RoleLink = [];
            foreach ($roleLinks as $link) {
                $RoleLink[$link['admin_id']][] = [
                    'id' => $link['role_id'],
                    'name' => $link['role_name'],
                    'level' => (int)$link['level'],
                    'delegable' => (int)$link['delegable'],
                ];
            }
            foreach ($list as &$item) {
                $item['roles'] = $RoleLink[$item['id']] ?? [];
            }
        }
        //返回数据
        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取管理员信息
     * @author zhaoyj
     * @version v1
     * @param int id - 管理员id
     * @return array|null
     * @return int id - 管理员id
     * @return string name - 管理员名称
     * @return string email - 管理员邮箱
     * @return string phone - 手机号
     * @return int status - 状态码
     * @return int last_login_time -  最后登录时间
     * @return string last_login_ip - 最后登录ip
     * @return int create_time - 创建管理员时间
     * @return array roles. - 管理员角色信息
     * @return int roles.id - 规则id
     * @return string roles.name - 规则名称
     */
    public function GetAdminInfo(int $id)
    {
        $AdminModel = new AdminModel();
        $admin = $AdminModel->field('id, name, email, phone, status, last_login_time, last_login_ip, create_time')
            ->where('id', $id)
            ->find();

        if (!$admin) {
            return null;
        }

        $admin['roles'] = (new AdminRoleLinkModel())->alias('arlk')
            ->leftJoin('admin_role arl', 'arl.id = arlk.role_id')
            ->where('arlk.admin_id', $id)
            ->field('arl.id, arl.name, arl.level, arl.delegable')
            ->select()
            ->toArray();

        return $admin;
    }

    /**
     * 创建管理员
     * @author zhaoyj
     * @version v1
     * @param array $params
     * @return int id - 管理员自增id
     * @throws \Exception
     */
    public function CreateAdmin(int $actorId, array $params): int
    {
        $AdminModel = new AdminModel();
        $AdminModel->startTrans();
        try {
            $this->delegationService->lockDelegationMutex();
            $roleIds = $this->delegationService->assertRolesAssignable($actorId, $params['role_ids'] ?? []);
            $id = (int)$AdminModel->insertGetId([
                'name' => $params['name'], //管理员名称
                'email' => $params['email'] ?? '', //管理员邮件
                'phone' => $params['phone'] ?? '', //管理员手机
                'password' => cmf_password($params['password']), //加密后的管理员密码
                'status' => $params['status'] ?? 1, //管理员状态
                'create_time' => time(), //管理员创建时间
                'update_time' => time(), //管理员更新时间
            ]);
            if (!empty($roleIds)) {
                $links = [];
                foreach ($roleIds as $roleId) {
                    $links[] = ['admin_id' => $id, 'role_id' => (int)$roleId];
                }
                $AdminModel->name('admin_role_link')->insertAll($links);
            }
            $AdminModel->commit();
            return $id;
        } catch (\Throwable $e) {
            $AdminModel->rollback();
            throw $e;
        }
    }

    /**
     * 修改管理员信息
     * @author zhaoyj
     * @version v1
     * @param int id - 管理员id
     * @param array $params - 参考\app\admin\controller\AdminController::UpdateAdmin下注释
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function UpdateAdmin(int $actorId, int $id, array $params): void
    {
        $AdminModel = new AdminModel();
        $AdminModel->startTrans();
        try {
            $this->delegationService->lockDelegationMutex();
            $this->delegationService->lockRoleMembers($actorId, [$id]);
            $this->delegationService->assertCanManageAdmin($actorId, $id);
            $roleIds = isset($params['role_ids'])
                ? $this->delegationService->assertRolesAssignable($actorId, $params['role_ids'])
                : null;

            $AdminModel = $AdminModel->where('id', $id)->lock(true)->find();

            if (!$AdminModel) {
                throw new \think\exception\ValidateException(lang('admin_not_found'));
            }
            $data = [];
            foreach (['name', 'email', 'phone', 'status'] as $field) {
                if (isset($params[$field])) {
                    $data[$field] = $params[$field];
                }
            }
            if (!empty($params['password'])) {
                $data['password'] = cmf_password($params['password']);
            }

            $AdminModel->save($data);

            $mustInvalidateSessions = isset($data['password'])
                || (isset($data['status']) && (int)$data['status'] !== 1)
                || $roleIds !== null;

            if ($roleIds !== null) {
                $AdminRoleLinkModel = new AdminRoleLinkModel();
                $AdminRoleLinkModel->where('admin_id', $id)->delete();
                if (!empty($roleIds)) {
                    $links = [];
                    foreach ($roleIds as $roleId) {
                        $links[] = ['admin_id' => $id, 'role_id' => (int)$roleId];
                    }
                    $AdminRoleLinkModel->insertAll($links);
                }
            }
            if ($mustInvalidateSessions) {
                JwtService::invalidateSessions('admin', $id);
            }

            $AdminModel->commit();
            if ($roleIds !== null) {
                Cache::delete('admin_rules:' . $id);
            }
        } catch (\Throwable $e) {
            $AdminModel->rollback();
            throw $e;
        }
    }

    /**
     * 删除管理员
     * @author zhaoyj
     * @version v1
     * @param int $id - 管理员自增主键id
     * @return void
     * @throws \Throwable
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function DeleteAdmin(int $actorId, int $id): void
    {
        $AdminModel = new AdminModel();
        $AdminModel->startTrans();
        try {
            $this->delegationService->lockDelegationMutex();
            $this->delegationService->lockRoleMembers($actorId, [$id]);
            $this->delegationService->assertCanManageAdmin($actorId, $id);

            if ($id === 1) {
                throw new \think\exception\ValidateException(lang('cannot_delete_super_admin'));
            }

            $admin = $AdminModel->where('id', $id)->lock(true)->find();
            if (!$admin) {
                throw new \think\exception\ValidateException(lang('admin_not_found'));
            }

            $AdminModel->where('id', $id)->delete();
            (new AdminRoleLinkModel())->where('admin_id', $id)->delete();
            JwtService::invalidateSessions('admin', $id);
            $AdminModel->commit();
            Cache::delete('admin_rules:' . $id);
        } catch (\Throwable $e) {
            $AdminModel->rollback();
            throw $e;
        }
    }
}