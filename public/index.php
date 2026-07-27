<?php /** @noinspection PhpRedundantOptionalArgumentInspection */
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\App;

// [ 应用入口文件 ]
const APP_VERSION = '1.0.0';
const APP_NAME = 'Cloud-Manager-System';
const APP_FULL_VERSION = APP_NAME . '@' . APP_VERSION;

require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->debug(true)->http;

$response = $http->run();

$response->send();

$http->end($response);
