<?php
// +----------------------------------------------------------------------
// | 插件配置
// +----------------------------------------------------------------------

return [
    // 插件类型
    'types' => [
        'addon', 'gateway', 'sms', 'mail', 'captcha',
        'certification', 'oauth', 'oss', 'invoice',
        'server', 'reserver',
    ],

    // 插件命名空间映射（根目录 plugins/）
    'namespaces' => [
        'addon' => root_path() . 'plugins/addon/',
        'gateway' => root_path() . 'plugins/gateway/',
        'server' => root_path() . 'plugins/server/',
        'reserver' => root_path() . 'plugins/reserver/',
        'sms' => root_path() . 'plugins/sms/',
        'mail' => root_path() . 'plugins/mail/',
        'captcha' => root_path() . 'plugins/captcha/',
        'certification' => root_path() . 'plugins/certification/',
        'oauth' => root_path() . 'plugins/oauth/',
        'oss' => root_path() . 'plugins/oss/',
        'invoice' => root_path() . 'plugins/invoice/',
    ],
];
