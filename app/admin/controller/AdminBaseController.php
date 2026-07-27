<?php
namespace app\admin\controller;

use app\BaseController;

/**
 * 管理后台基类
 */
abstract class AdminBaseController extends BaseController
{
    protected function initialize()
    {
        parent::initialize();
    }
    /**
     * @title 合并分页参数
     * @desc 合并分页参数
     * @author zhaoyj
     * @version v1
     * @return array
     * @return int page - 页数
     * @return int limit - 页数最大限制
     * @return string sort - 排序，升序asc 降序desc
     */
    protected function MergeParams(): array
    {
        return [
            'page' => max(1, (int)$this->request->param('page', 1)),
            'limit' => min(max(1, (int)$this->request->param('limit', 20)), 100),
            'sort' => in_array($this->request->param('sort'), ['asc', 'desc'])
                ? $this->request->param('sort')
                : 'desc',
        ];
    }
    /**
     * @title 获取管理员id
     * @desc 获取管理员id
     * @author zhaoyj
     * @version v1
     * @return int - 管理员id
     */
    protected function adminId(): int
    {
        return $this->request->adminId ?? 0;
    }
    /**
     * @title 获取管理员名称
     * @desc 获取管理员名称
     * @author zhaoyj
     * @version v1
     * @return string - 管理员名称
     */
    protected function adminName(): string
    {
        return $this->request->adminName ?? '';
    }
}