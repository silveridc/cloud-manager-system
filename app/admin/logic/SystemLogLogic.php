<?php
namespace app\admin\logic;

use app\admin\model\SystemLogModel;

/**
 * 系统日志逻辑
 * @desc 系统日志逻辑
 * @uses \app\admin\logic\SystemLogLogic
 */
class SystemLogLogic
{
    /**
     * 获取系统日志列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @param string params.keywords - 搜索关键字
     * @param string params.type - 日志类型
     * @param int params.admin_id - 管理员id
     * @param string params.start_time - 开始时间
     * @param string params.end_time - 结束时间
     * @param string params.sort - 排序规则
     * @param int params.page - 页码
     * @param int params.limit - 每页条数
     * @return array
     * @return array list - 日志列表
     * @return int count - 总数量
     */
    public function GetLogList(array $params): array
    {
        $SystemLogModel = new SystemLogModel();
        $query = $SystemLogModel;

        if (!empty($params['keywords'])) {
            $keywords = str_replace(['%', '_'], ['\\%', '\\_'], $params['keywords']);
            $query->where('description', 'like', '%' . $keywords . '%');
        }
        if (!empty($params['type'])) {
            $query->where('type', $params['type']);
        }
        if (!empty($params['admin_id'])) {
            $query->where('admin_id', (int)$params['admin_id']);
        }
        if (!empty($params['start_time'])) {
            $query->where('create_time', '>=', strtotime($params['start_time']));
        }
        if (!empty($params['end_time'])) {
            $query->where('create_time', '<=', strtotime($params['end_time']));
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 记录操作日志
     * @author zhaoyj
     * @version v1
     * @param string $description - 日志描述
     * @param string $type - 日志类型
     * @param int $relId - 关联id
     * @param int $adminId - 管理员id
     * @param int $clientId - 客户id
     * @return void
     */
    public function record(string $description, string $type = '', int $relId = 0, int $adminId = 0, int $clientId = 0): void
    {
        $SystemLogModel = new SystemLogModel();
        $SystemLogModel->insert([
            'description' => $description,
            'type' => $type,
            'rel_id' => $relId,
            'admin_id' => $adminId,
            'client_id' => $clientId,
            'ip' => request()->ip(),
            'create_time' => time(),
        ]);
    }
}