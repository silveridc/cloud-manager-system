<?php
namespace app\admin\logic;

use app\admin\model\EmailTemplateModel;

/**
 * 邮件逻辑
 * @desc 邮件逻辑
 * @uses \app\admin\logic\EmailLogic
 */
class EmailLogic
{
    /**
     * 获取邮件模板列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function getTemplateList(array $params): array
    {
        $EmailTemplateModel = new EmailTemplateModel();

        $query = $EmailTemplateModel;

        if (!empty($params['keywords'])) {
            $query->where('name|subject', 'like', '%' . $params['keywords'] . '%');
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('id', $params['sort'] ?? 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取邮件模板详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 模板ID
     * @return array|null
     */
    public function GetEmailTemplateInfo(int $id): ?array
    {
        $EmailTemplateModel = new EmailTemplateModel();

        return $EmailTemplateModel->where('id', $id)->find() ?: null;
    }

    /**
     * 更新邮件模板
     * @author zhaoyj
     * @version v1
     * @param int $id - 模板ID
     * @param array $params - 更新参数
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function updateTemplate(int $id, array $params)
    {
        $EmailTemplateModel = new EmailTemplateModel();

        $tpl = $EmailTemplateModel->where('id', $id)->find();
        if (!$tpl) {
            return [
                'status' => 404,
                'messages' => lang('template_not_found'),
                'time' => time(),
            ];
        }

        $updateData = [];
        $allowFields = ['name', 'subject', 'content', 'status'];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if ($updateData) {
            $EmailTemplateModel->where('id', $id)->update($updateData);
        }
    }
}