<?php
// 中间件配置
return [
    // 别名或分组
    'alias' => [
        'cors' => \app\http\middleware\Cors::class,
        'admin_auth' => \app\http\middleware\AdminAuth::class,
        'client_auth' => \app\http\middleware\ClientAuth::class,
        'optional_auth' => \app\http\middleware\OptionalAuth::class,
        'pagination' => \app\http\middleware\Pagination::class,
        'throttle' => \app\http\middleware\ThrottleRepeat::class,
        'operate_pwd' => \app\http\middleware\OperatePassword::class,
        'maintenance' => \app\http\middleware\MaintenanceMode::class,
    ],
    // 优先级设置，此数组中的中间件会按照数组中的顺序优先执行
    'priority' => [
        \app\http\middleware\Cors::class,
        \app\http\middleware\AdminAuth::class,
        \app\http\middleware\ClientAuth::class,
        \app\http\middleware\OptionalAuth::class,
        \app\http\middleware\Pagination::class,
        \app\http\middleware\ThrottleRepeat::class,
        \app\http\middleware\OperatePassword::class,
        \app\http\middleware\MaintenanceMode::class,
    ],
];
