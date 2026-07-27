<?php
namespace app\admin\logic;

use app\admin\model\FeedbackModel;
use app\admin\model\FeedbackTypeModel;

/**
 * 反馈逻辑
 * @desc 反馈逻辑
 * @uses \app\admin\logic\FeedbackLogic
 */
class FeedbackLogic
{
    /**
     * 获取反馈列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数（status, keywords, sort, page, limit）
     * @return array 包含list和count的数组
     */
    public function getList(array $params): array
    {
        $FeedbackModel = new FeedbackModel();

        $query = $FeedbackModel->alias('f')
            ->leftJoin('client c', 'c.id = f.client_id')
            ->leftJoin('feedback_type ft', 'ft.id = f.type_id')
            ->field([
                'f.id', 'f.title', 'f.content', 'f.type_id', 'f.client_id',
                'f.status', 'f.create_time',
                'c.username as client_name',
                'ft.name as type_name',
            ]);

        if (!empty($params['status'])) {
            $query->where('f.status', $params['status']);
        }
        if (!empty($params['keywords'])) {
            $query->where('f.title|f.content', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('f.id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建反馈
     * @author zhaoyj
     * @version v1
     * @param array $params - 反馈参数（title, content, type_id）
     * @param int $clientId - 客户ID
     * @return int 新创建的反馈ID
     */
    public function create(array $params, int $clientId): int
    {
        $FeedbackModel = new FeedbackModel();

        return (int)$FeedbackModel->insertGetId([
            'title' => $params['title'] ?? '',
            'content' => $params['content'],
            'type_id' => $params['type_id'] ?? 0,
            'client_id' => $clientId,
            'status' => 'pending',
            'create_time' => time(),
        ]);
    }

    /**
     * 获取反馈类型列表
     * @author zhaoyj
     * @version v1
     * @return array 反馈类型列表，按order字段升序排列
     */
    public function getTypeList(): array
    {
        $FeedbackTypeModel = new FeedbackTypeModel();

        return $FeedbackTypeModel
            ->order('order', 'asc')
            ->select()->toArray();
    }
}