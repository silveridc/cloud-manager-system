<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------
return [
    // 应用地址
    'app_host'         => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 应用快速访问
    'app_express'      => true,
    // 默认应用
    'default_app' => 'home',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    'admin_path' => 'admin',
    // 应用映射（自动多应用模式有效）
    'app_map' => [
        config('app.admin_path','admin') => 'admin',
    ],
    // 域名绑定
    'domain_bind'      => [],
    // 禁止URL访问的应用列表
    'deny_app_list'    => [],
    // 异常页面的模板文件
    'exception_tmpl' => app()->getThinkPath() . 'tpl/think_exception.tpl',
    // 错误显示信息
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => false,
    // 语言配置
    'app_lang' => 'zh-cn',

    // 订单类型
    'order_type' => ['new', 'renew', 'upgrade', 'artificial', 'recharge', 'on_demand', 'change_billing_cycle'],

    // 订单状态
    'order_status' => ['Unpaid', 'Paid', 'Cancelled', 'Refunded', 'WaitUpload', 'WaitReview', 'ReviewFail'],

    // 产品实例状态
    'host_status' => ['Unpaid', 'Pending', 'Active', 'Suspended', 'Deleted', 'Failed', 'Cancelled', 'Grace', 'Keep'],

    // 计费周期
    'billing_cycle' => ['free', 'onetime', 'recurring_prepayment', 'recurring_postpaid', 'on_demand'],

    // 任务状态
    'task_status' => ['Wait', 'Exec', 'Finish', 'Failed'],

    // 通知类别
    'notice_category' => [
        'client_account', 'order_pay', 'host', 'credit_limit',
        'maintenance', 'ticket', 'finance', 'other',
    ],

    // 分页默认值
    'page' => 1,
    'limit' => 20,
];
