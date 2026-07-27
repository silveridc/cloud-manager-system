<?php

namespace app\home\controller;

use app\BaseController;

/**
 * 客户端基础控制器
 * @desc 客户端基础控制器
 * @uses \app\home\controller\HomeBaseController
 */
abstract class HomeBaseController extends BaseController
{

    protected function success(mixed $data = [], string $messages = '')
    {
        return json([
            'status' => 200,
            'messages' => $messages ?: lang('success_message'),
            'data' => $data,
            'time' => time(),
        ]);
    }

    protected function error(string $messages = '', int $status = 400, mixed $data = [])
    {
        return json([
            'status' => $status,
            'messages' => $messages ?: lang('fail_message'),
            'data' => $data,
            'time' => time(),
        ], $status);
    }

    protected function paginationParams(): array
    {
        return [
            'page' => max(1, (int)$this->request->param('page', 1)),
            'limit' => min(max(1, (int)$this->request->param('limit', 20)), 100),
            'sort' => in_array($this->request->param('sort'), ['asc', 'desc'])
                ? $this->request->param('sort')
                : 'desc',
        ];
    }

    protected function clientId(): int
    {
        return $this->request->clientId ?? 0;
    }

    protected function clientName(): string
    {
        return $this->request->clientName ?? '';
    }
}