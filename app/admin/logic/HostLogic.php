<?php
namespace app\admin\logic;

use app\common\entity\HostEntity;
use app\common\model\HostModel;
use app\common\enum\HostStatus;
use think\facade\Event;

/**
 * 产品实例业务逻辑
 * @desc 产品实例业务逻辑
 * @uses \app\admin\logic\HostLogic
 */
class HostLogic
{
    protected HostEntity $entity;
    protected HostModel $model;

    public function __construct(HostEntity $entity, HostModel $model)
    {
        $this->entity = $entity;
        $this->model = $model;
    }

    /**
     * 获取产品实例列表（管理端）
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数（分页、筛选条件等）
     * @return array
     * @return array list - 产品实例列表
     * @return int count - 实例总数
     */
    public function GetHostList(array $params): array
    {
        return $this->entity->listForAdmin($params);
    }

    /**
     * 获取客户产品实例列表（客户端）
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params - 查询参数（分页、筛选条件等）
     * @return array
     * @return array list - 产品实例列表
     * @return int count - 实例总数
     */
    public function getClientList(int $clientId, array $params): array
    {
        return $this->entity->listForClient($clientId, $params);
    }

    /**
     * 获取产品实例详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品实例ID
     * @param int|null $clientId - 客户ID（传入时验证归属关系，null表示管理端不限制）
     * @return array|null
     * @return int id - 实例ID
     * @return int client_id - 客户ID
     * @return string status - 实例状态
     * @return string notes - 备注
     */
    public function GetHostInfo(int $id, ?int $clientId = null): ?array
    {
        return $this->entity->detail($id, $clientId);
    }

    /**
     * 暂停产品实例（仅活跃状态可暂停）
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品实例ID
     * @param string $reason - 暂停原因（默认为空）
     * @return void
     * @throws \think\exception\ValidateException 实例不存在或非活跃状态时抛出
     * @throws \Throwable 数据库事务异常时抛出
     */
    public function SuspendHost(int $id, string $reason = '')
    {
        $host = $this->model->find($id);
        if (!$host) {
            return [
                'status' => 404,
                'messages' => lang('host_not_found'),
                'time' => time(),
            ];
        }

        if ($host->getAttr('status') !== HostStatus::Active->value) {
            return [
                'status' => 400,
                'messages' => lang('host_cannot_suspend'),
                'time' => time(),
            ];
        }

        $host->startTrans();
        try {
            $host->save([
                'status' => HostStatus::Suspended->value,
                'suspend_reason' => $reason,
                'suspend_time' => time(),
            ]);

            Event::trigger('after_host_suspend', [
                'host_id' => $id,
                'client_id' => $host->getAttr('client_id'),
                'reason' => $reason,
            ]);

            $host->commit();
        } catch (\Throwable $e) {
            $host->rollback();
            throw $e;
        }
    }

    /**
     * 解除产品实例暂停（仅暂停状态可解除）
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品实例ID
     * @return void
     * @throws \think\exception\ValidateException 实例不存在或非暂停状态时抛出
     * @throws \Throwable 数据库事务异常时抛出
     */
    public function UnsuspendHost(int $id)
    {
        $host = $this->model->find($id);
        if (!$host) {
            return [
                'status' => 404,
                'messages' => lang('host_not_found'),
                'time' => time(),
            ];
        }

        if ($host->getAttr('status') !== HostStatus::Suspended->value) {
            return [
                'status' => 400,
                'messages' => lang('host_not_suspended'),
                'time' => time(),
            ];
        }

        $host->startTrans();
        try {
            $host->save([
                'status' => HostStatus::Active->value,
            ]);

            Event::trigger('after_host_unsuspend', [
                'host_id' => $id,
                'client_id' => $host->getAttr('client_id'),
            ]);

            $host->commit();
        } catch (\Throwable $e) {
            $host->rollback();
            throw $e;
        }
    }

    /**
     * 终止产品实例（已终止的不可重复终止）
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品实例ID
     * @return void
     * @throws \think\exception\ValidateException 实例不存在或已终止时抛出
     * @throws \Throwable 数据库事务异常时抛出
     */
    public function TerminateHost(int $id)
    {
        $host = $this->model->find($id);
        if (!$host) {
            return [
                'status' => 404,
                'messages' => lang('host_not_found'),
                'time' => time(),
            ];
        }

        if ($host->getAttr('status') === HostStatus::Deleted->value) {
            return [
                'status' => 400,
                'messages' => lang('host_already_terminated'),
                'time' => time(),
            ];
        }

        $host->startTrans();
        try {
            $host->save([
                'status' => HostStatus::Deleted->value,
                'terminate_time' => time(),
            ]);

            Event::trigger('after_host_terminate', [
                'host_id' => $id,
                'client_id' => $host->getAttr('client_id'),
            ]);

            $host->commit();
        } catch (\Throwable $e) {
            $host->rollback();
            throw $e;
        }
    }

    /**
     * 更新产品实例备注
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品实例ID
     * @param string $notes - 备注内容
     * @param int|null $clientId - 客户ID（传入时验证归属关系，null表示管理端不限制）
     * @return void
     * @throws \think\exception\ValidateException 实例不存在或不属于该客户时抛出
     */
    public function UpdateHostNotes(int $id, string $notes, ?int $clientId = null)
    {
        $query = $this->model->where('id', $id);
        if ($clientId !== null) {
            $query->where('client_id', $clientId);
        }

        $host = $query->find();
        if (!$host) {
            return [
                'status' => 404,
                'messages' => lang('host_not_found'),
                'time' => time(),
            ];
        }

        $host->save(['notes' => $notes]);
    }
}
