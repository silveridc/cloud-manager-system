<?php
namespace app\admin\logic;

use app\admin\model\SmsTemplateModel;

/**
 * 短信逻辑
 * @uses \app\admin\logic\SmsLogic
 */
class SmsLogic
{
    /**
     * 获取短信模板列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function getTemplateList(array $params): array
    {
        $SmsTemplateModel = new SmsTemplateModel();

        $query = $SmsTemplateModel;

        if (!empty($params['keywords'])) {
            $query->where('name|content', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取短信模板详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 模板ID
     * @return array|null
     */
    public function GetSmsTemplateInfo(int $id): ?array
    {
        $SmsTemplateModel = new SmsTemplateModel();

        return $SmsTemplateModel->where('id', $id)->find() ?: null;
    }

    /**
     * 更新短信模板
     * @author zhaoyj
     * @version v1
     * @param int $id - 模板ID
     * @param array $params - 模板参数
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function updateTemplate(int $id, array $params)
    {
        $SmsTemplateModel = new SmsTemplateModel();

        $tpl = $SmsTemplateModel->where('id', $id)->find();
        if (!$tpl) {
            return [
                'status' => 404,
                'messages' => lang('template_not_found'),
                'time' => time(),
            ];
        }

        $updateData = [];
        $allowFields = ['name', 'content', 'status'];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if ($updateData) {
            $SmsTemplateModel->where('id', $id)->update($updateData);
        }
    }
}