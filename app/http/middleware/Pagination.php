<?php

namespace app\http\middleware;

use think\Request;
use think\Response;

/**
 * 分页中间件
 * @desc 分页中间件
 * @uses \app\http\middleware\Pagination
 */
class Pagination
{
    public function handle(Request $request, \Closure $next): Response
    {
        // 标准化分页参数到 request 属性
        $page = max(1, (int)$request->param('page', 1));
        $limit = min(max(1, (int)$request->param('limit', 20)), 100);
        $sort = in_array($request->param('sort'), ['asc', 'desc']) ? $request->param('sort') : 'desc';
        $orderBy = $request->param('orderby', 'id');

        // 白名单限制排序字段，防止 ORDER BY 注入
        $allowedOrderBy = ['id', 'create_time', 'update_time', 'name', 'status', 'amount', 'order', 'credit'];
        if (!in_array($orderBy, $allowedOrderBy)) {
            $orderBy = 'id';
        }

        $request->page = $page;
        $request->limit = $limit;
        $request->sort = $sort;
        $request->orderBy = $orderBy;

        return $next($request);
    }
}