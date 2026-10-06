<?php

namespace app\admin\service;

use app\admin\model\AdminModel;
use app\admin\model\AdminRoleLinkModel;
use app\admin\model\AdminRoleModel;
use app\admin\model\AdminRuleModel;
use think\exception\ValidateException;

/**
 * 管理员角色层级与权限委派策略。
 */
class AdminDelegationService
{
    public function lockDelegationMutex(): void
    {
        $superAdmin = (new AdminModel())->where('id', 1)->lock(true)->find();
        if (!$superAdmin) {
            throw new ValidateException(lang('admin_not_found'));
        }
    }

    public function lockRoleMembers(int $actorId, array $memberIds): void
    {
        $adminIds = array_values(array_unique(array_merge([$actorId], array_map('intval', $memberIds))));
        sort($adminIds, SORT_NUMERIC);

        $admins = (new AdminModel())
            ->whereIn('id', $adminIds)
            ->order('id', 'asc')
            ->lock(true)
            ->select();

        $activeActor = false;
        foreach ($admins as $admin) {
            if ((int)$admin->getAttr('id') === $actorId && (int)$admin->getAttr('status') === 1) {
                $activeActor = true;
                break;
            }
        }

        if (!$activeActor) {
            throw new ValidateException(lang('permission_denied'));
        }
    }

    public function assertCanManageAdmin(int $actorId, int $targetId): void
    {
        if ($actorId === 1) {
            return;
        }

        if ($targetId === 1 || $targetId === $actorId) {
            throw new ValidateException(lang('admin_manage_denied'));
        }

        $target = (new AdminModel())->where('id', $targetId)->lock(true)->find();
        if (!$target || $this->effectiveLevel($targetId) >= $this->effectiveLevel($actorId)) {
            throw new ValidateException(lang('admin_manage_denied'));
        }

        if (array_diff($this->rulesForAdmin($targetId), $this->rulesForAdmin($actorId))) {
            throw new ValidateException(lang('target_permission_exceeds_actor'));
        }
    }

    public function assertRolesAssignable(int $actorId, mixed $roleIds): array
    {
        $this->lockActor($actorId);

        if (!is_array($roleIds)) {
            throw new ValidateException(lang('invalid_role_ids'));
        }

        $roleIds = $this->normalizeIds($roleIds);
        if (empty($roleIds)) {
            return [];
        }

        $roles = (new AdminRoleModel())
            ->whereIn('id', $roleIds)
            ->order('id', 'asc')
            ->lock(true)
            ->select()
            ->toArray();
        if (count($roles) !== count($roleIds)) {
            throw new ValidateException(lang('role_not_found'));
        }

        if ($actorId === 1) {
            return $roleIds;
        }

        $actorLevel = $this->effectiveLevel($actorId);
        $actorRules = $this->rulesForAdmin($actorId);
        foreach ($roles as $role) {
            if ((int)$role['delegable'] !== 1 || (int)$role['level'] >= $actorLevel) {
                throw new ValidateException(lang('role_not_delegable'));
            }

            $roleRules = (new AdminRuleModel())->where('role_id', $role['id'])->column('name');
            if (array_diff($roleRules, $actorRules)) {
                throw new ValidateException(lang('role_permission_exceeds_actor'));
            }
        }

        return $roleIds;
    }

    public function assertCanManageRole(int $actorId, int $roleId): void
    {
        $role = (new AdminRoleModel())->where('id', $roleId)->lock(true)->find();
        if (!$role) {
            throw new ValidateException(lang('role_not_found'));
        }

        if ($actorId === 1) {
            return;
        }

        if ((int)$role->getAttr('delegable') !== 1) {
            throw new ValidateException(lang('role_not_delegable'));
        }

        $actorLevel = $this->effectiveLevel($actorId);
        $actorRules = $this->rulesForAdmin($actorId);
        $memberIds = (new AdminRoleLinkModel())->where('role_id', $roleId)->column('admin_id');
        foreach ($memberIds as $memberId) {
            $memberId = (int)$memberId;
            if ($memberId === $actorId) {
                throw new ValidateException(lang('cannot_manage_own_role'));
            }
            if ($memberId === 1 || $this->effectiveLevel($memberId) >= $actorLevel) {
                throw new ValidateException(lang('role_has_protected_members'));
            }
            if (array_diff($this->rulesForAdmin($memberId), $actorRules)) {
                throw new ValidateException(lang('role_has_protected_members'));
            }
        }

        if ((int)$role->getAttr('level') >= $actorLevel) {
            throw new ValidateException(lang('role_manage_denied'));
        }
    }

    public function assertRoleDefinitionAllowed(int $actorId, mixed $level, mixed $rules): array
    {
        $this->lockActor($actorId);

        if (!is_array($rules)) {
            throw new ValidateException(lang('invalid_role_rules'));
        }

        $level = $this->normalizeLevel($level);
        $rules = $this->normalizeRules($rules);
        if ($actorId !== 1) {
            if ($level >= $this->effectiveLevel($actorId)) {
                throw new ValidateException(lang('role_level_too_high'));
            }

            if (array_diff($rules, $this->rulesForAdmin($actorId))) {
                throw new ValidateException(lang('role_permission_exceeds_actor'));
            }
        }

        return ['level' => $level, 'rules' => $rules];
    }

    public function normalizeDelegable(int $actorId, mixed $delegable): int
    {
        if (!in_array($delegable, [0, 1, '0', '1', false, true], true)) {
            throw new ValidateException(lang('invalid_delegable'));
        }

        $delegable = (int)(bool)$delegable;
        if ($actorId !== 1 && $delegable !== 1) {
            throw new ValidateException(lang('delegated_role_must_remain_delegable'));
        }

        return $delegable;
    }

    public function effectiveLevel(int $adminId): int
    {
        if ($adminId === 1) {
            return PHP_INT_MAX;
        }

        $roleIds = (new AdminRoleLinkModel())->where('admin_id', $adminId)->column('role_id');
        if (empty($roleIds)) {
            return 0;
        }

        return (int)(new AdminRoleModel())->whereIn('id', $roleIds)->max('level');
    }

    private function rulesForAdmin(int $adminId): array
    {
        $roleIds = (new AdminRoleLinkModel())->where('admin_id', $adminId)->column('role_id');
        if (empty($roleIds)) {
            return [];
        }

        return $this->normalizeRules(
            (new AdminRuleModel())->whereIn('role_id', $roleIds)->column('name')
        );
    }

    private function lockActor(int $actorId): void
    {
        $actor = (new AdminModel())->where('id', $actorId)->where('status', 1)->lock(true)->find();
        if (!$actor) {
            throw new ValidateException(lang('permission_denied'));
        }
    }

    private function normalizeLevel(mixed $level): int
    {
        if (filter_var($level, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 9999]]) === false) {
            throw new ValidateException(lang('invalid_role_level'));
        }

        return (int)$level;
    }

    private function normalizeIds(array $ids): array
    {
        $normalized = [];
        foreach ($ids as $id) {
            if (filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                throw new ValidateException(lang('invalid_role_ids'));
            }
            $normalized[] = (int)$id;
        }

        return array_values(array_unique($normalized));
    }

    private function normalizeRules(array $rules): array
    {
        $normalized = [];
        foreach ($rules as $rule) {
            if (!is_string($rule) && !is_numeric($rule)) {
                throw new ValidateException(lang('invalid_role_rules'));
            }

            $rule = trim((string)$rule);
            if ($rule !== '') {
                $normalized[] = $rule;
            }
        }

        return array_values(array_unique($normalized));
    }
}
