<?php
namespace app\admin\logic;
use think\facade\Cache;
use app\common\service\JwtService;
use app\admin\entity\ClientEntity;
use app\common\model\ClientModel;
use app\common\model\HostModel;
use think\facade\Event;
/**
 * 客户业务逻辑
 * @desc 客户业务逻辑
 * @uses \app\admin\logic\ClientLogic
 */
class ClientLogic
{
    protected ClientEntity $ClientEntity;
    protected ClientModel $ClientModel;

    public function __construct(ClientEntity $ClientEntity, ClientModel $ClientModel)
    {
        $this->ClientEntity = $ClientEntity;
        $this->ClientModel = $ClientModel;
    }

    /**
     * @title 管理员获取客户列表
     * @desc 管理员获取客户列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数（分页、筛选条件等）
     * @return array
     * @return array list - 客户列表
     * @return int count - 客户总数
     */
    public function GetClientList(array $params): array
    {
        return $this->ClientEntity->listForAdmin($params);
    }

    /**
     * @title 获取客户详情
     * @desc 获取客户详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 客户ID
     * @return array|null
     * @return int id - 客户ID
     * @return string username - 用户名
     * @return string email - 邮箱
     * @return string phone - 手机号
     * @return int status - 状态
     * @return float credit - 余额
     */
    public function GetClientInfo(int $id): ?array
    {
        return $this->ClientEntity->detail($id);
    }

    /**
     * @title 创建客户
     * @desc 创建客户
     * @author zhaoyj
     * @version v1
     * @param array $params .notes - 备注（可选）
     * @return int 新创建的客户ID
     * @throws \Throwable 数据库事务异常时抛出
     */
    public function CreateClient(array $params): array
    {
        $this->ClientModel->startTrans();
        try {
            $this->ClientModel->save([
                'username' => $params['username'],
                'email' => $params['email'] ?? '',
                'phone_code' => $params['phone_code'] ?? '',
                'phone' => $params['phone'] ?? '',
                'password' => cmf_password($params['password']),
                'status' => 1,
                'credit' => 0,
                'company' => $params['company'] ?? '',
                'address' => $params['address'] ?? '',
                'language' => $params['language'] ?? 'zh-cn',
                'country_id' => $params['country_id'] ?? 0,
                'notes' => $params['notes'] ?? '',
            ]);
            $ClientID = (int) $this->ClientModel->id;
            Event::trigger('after_client_create', ['client_id' => $ClientID]);
            $this->ClientModel->commit();
            return [
                'status' => 200,
                'messages' => lang('success_message'),
                'id' => $ClientID,
                'time' => time(),
            ];
            } catch (\think\db\exception\DuplicateException) {
                $this->ClientModel->rollback();
                throw new \think\exception\HttpException(400, '该用户名已被占用，请尝试其他用户名');

            } catch (\Throwable $e) {
            $this->ClientModel->rollback();
            throw $e;
        }
    }

    /**
     * @title 更新客户信息
     * @desc 更新客户信息
     * @author zhaoyj
     * @version v1
     * @param int $id - 客户ID
     * @param array $params .password - 新密码（可选）
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function UpdateClient(int $id, array $params)
    {
        $client = $this->ClientModel->find($id);
        if (!$client) {
            return [
                'status' => 404,
                'messages' => lang('client_not_found'),
                'time' => time(),
            ];
        }
        $updateData = [];
        $allowFields = ['username', 'email', 'phone_code', 'phone', 'company', 'address', 'language', 'country_id', 'notes', 'status'];

        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }
        $NewPassword = $params['password'] ?? '';
        if (!empty($NewPassword)) {
            $updateData['password'] = cmf_password($NewPassword);
        }

        if (!empty($updateData)) {
            $client->save($updateData);
            if (isset($updateData['password']) || (isset($updateData['status']) && (int)$updateData['status'] !== 1)) {
                JwtService::invalidateSessions('client', $id);
            }
            Event::trigger('after_client_update', ['client_id' => $id, 'data' => $updateData]);
        }
        return [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
    }

    /**
     * @title 禁用客户
     * @desc 禁用客户
     * @author zhaoyj
     * @version v1
     * @param int $id - 客户ID
     * @return void
     * @throws \Throwable
     */
    public function DisabledClient(int $id)
    {
        $client = $this->ClientModel->find($id);
        if (!$client) {
            return [
                'status' => 404,
                'messages' => lang('client_not_found'),
                'time' => time(),
            ];
        }

        $HostModel = new HostModel();
        $activeHosts = $HostModel
            ->where('client_id', $id)
            ->whereNotIn('status', ['Deleted', 'Cancelled'])
            ->count();

        if ($activeHosts > 0) {
            return [
                'status' => 400,
                'messages' => lang('client_has_active_hosts'),
                'time' => time(),
            ];
        }

        $client->startTrans();
        try {
            $client->save(['status' => 0]);
            JwtService::invalidateSessions('client', $id);
            Event::trigger('after_client_disabled', ['client_id' => $id]);
            $client->commit();
        } catch (\Throwable $e) {
            $client->rollback();
            throw $e;
        }
        return [
            'status' => 200,
            'messages' => lang('disable_success'),
            'time' => time(),
        ];
    }
    /**
     * @title 删除客户
     * @desc 删除客户
     * @author zhaoyj
     * @version v1
     * @param int $id - 客户ID
     * @return void
     * @throws \Throwable
     */
    public function DeleteClient(int $id)
    {
        $client = $this->ClientModel->find($id);
        if (!$client) {
            return [
                'status' => 404,
                'messages' => lang('client_not_found'),
                'time' => time(),
            ];
        }

        $HostModel = new HostModel();
        $activeHosts = $HostModel
            ->where('client_id', $id)
            ->whereNotIn('status', ['Deleted', 'Cancelled'])
            ->count();

        if ($activeHosts > 0) {
            return [
                'status' => 400,
                'messages' => lang('client_has_active_hosts'),
                'time' => time(),
            ];
        }

        $client->startTrans();
        try {
            $client->delete();
            JwtService::invalidateSessions('client', $id);
            Event::trigger('after_client_delete', ['client_id' => $id]);
            $client->commit();
        } catch (\Throwable $e) {
            $client->rollback();
            throw $e;
        }
        return [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
    }
}
