<?php
namespace app\admin\logic;

use app\admin\model\NoticeSettingModel;

/**
 * 通知设置逻辑
 * @desc 通知设置逻辑
 * @uses \app\admin\logic\NoticeSettingLogic
 */
class NoticeSettingLogic
{
    /**
     * 获取通知设置列表
     * @author zhaoyj
     * @version v1
     * @return array
     */
    public function GetNoticeSettingList(): array
    {
        $NoticeSettingModel = new NoticeSettingModel();

        return $NoticeSettingModel
            ->order('id', 'asc')
            ->select()->toArray();
    }

    /**
     * 更新通知设置
     * @author zhaoyj
     * @version v1
     * @param int $id - 设置ID
     * @param array $params - 更新参数
     * @return void
     * @throws \think\exception\ValidateException
     */
    public function UpdateNoticeSetting(int $id, array $params)
    {
        $NoticeSettingModel = new NoticeSettingModel();

        $setting = $NoticeSettingModel->where('id', $id)->find();
        if (!$setting) {
            return [
                'status' => 404,
                'messages' => lang('setting_not_found'),
                'time' => time(),
            ];
        }

        $updateData = [];
        $allowFields = ['sms_global', 'sms_account', 'email_global', 'email_account'];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if ($updateData) {
            $NoticeSettingModel->where('id', $id)->update($updateData);
        }
    }
}