<?php
namespace app\admin\logic;

use app\admin\model\ClientRecordModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 客户记录逻辑
 * @desc 客户记录逻辑
 * @uses \app\admin\logic\ClientRecordLogic
 */
class ClientRecordLogic
{
    /**
     * 获取客户记录列表
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params .sort - 排序方式（默认desc）
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function GetClientRecordList(int $clientId, array $params): array
    {
        $ClientRecordModel = new ClientRecordModel();

        $query = $ClientRecordModel
            ->where('client_id', $clientId);

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建客户记录
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param array $params .content - 记录内容
     * @param int $adminId - 操作管理员ID（默认0）
     * @return int 新创建的记录ID
     */
    public function CreateClientRecord(int $clientId, array $params, int $adminId = 0): int
    {
        $ClientRecordModel = new ClientRecordModel();

        return (int)$ClientRecordModel->insertGetId([
            'client_id' => $clientId,
            'admin_id' => $adminId,
            'content' => $params['content'] ?? '',
            'create_time' => time(),
        ]);
    }

    /**
     * 删除客户记录（验证归属关系防止越权）
     * @author zhaoyj
     * @version v1
     * @param int $clientId - 客户ID
     * @param int $id - 记录ID
     * @return array|void
     */
    public function DeleteClientRecord(int $clientId, int $id)
    {
        $ClientRecordModel = new ClientRecordModel();

        $record = $ClientRecordModel
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->find();
        if (!$record) {
            return [
                'status' => 404,
                'messages' => lang('record_not_found'),
                'time' => time(),
            ];
        }
        $ClientRecordModel->where('id', $id)->delete();
    }
}
