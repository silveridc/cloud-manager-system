<?php
return [
    'title'         => 'API 接口文档',
    'version'       => '1.0.0',
    'copyright'     => 'Cloud Manager System',
    'password'      => '',
    'controller'    => [
        // 在此处填写需要生成文档的控制器类名，例如：
        // \app\admin\controller\UserController::class,
    ],
    'filter_method' => ['_empty'],
    'public_header' => [],
    'public_param'  => [],
    'return_format' => [
        'status'  => '200/300/301/302',
        'message' => '提示信息',
    ],
    'static_path'   => '/doc-static/',
];
