<?php
use think\facade\Route;
Route::miss('IndexController/NotFound');
Route::get('/','IndexController/Index');
// 无需认证
Route::group('v1', function () {
    Route::post('admin/login', 'AuthController/AdminLogin');
})->middleware([
    \app\http\middleware\Cors::class,
]);
// 需要认证
Route::group('v1', function () {
    // 认证
    Route::post('admin/logout', 'AuthController/AdminLogout');
    Route::put('admin/changepassword', 'AuthController/AdminChangePassword');
    // 管理员
    Route::get('admin', 'AdminController/GetAdminList');
    Route::get('admin/:id', 'AdminController/GetAdminInfo')->pattern(['id' => '\d+']);
    Route::post('admin', 'AdminController/CreateAdmin');
    Route::put('admin/:id', 'AdminController/UpdateAdmin')->pattern(['id' => '\d+']);
    Route::delete('admin/:id', 'AdminController/DeleteAdmin')->pattern(['id' => '\d+']);
    // 角色
    Route::get('admin/role', 'AdminRoleController/GetAdminRoleList');
    Route::get('admin/role/:id', 'AdminRoleController/GetAdminRoleInfo')->pattern(['id' => '\d+']);
    Route::post('admin/role', 'AdminRoleController/CreateAdminRole');
    Route::put('admin/role/:id', 'AdminRoleController/UpdateAdminRole')->pattern(['id' => '\d+']);
    Route::delete('admin/role/:id', 'AdminRoleController/DeleteAdminRole')->pattern(['id' => '\d+']);
    // 客户管理
    Route::get('client', 'ClientController/GetClientList');
    Route::get('client/:id', 'ClientController/GetClientInfo')->pattern(['id' => '\d+']);
    Route::post('client', 'ClientController/CreateClient');
    Route::put('client/:id', 'ClientController/UpdateClient')->pattern(['id' => '\d+']);
    Route::delete('client/:id', 'ClientController/DeleteClient')->pattern(['id' => '\d+'])->middleware(\app\http\middleware\OperatePassword::class);
    // 产品管理
    Route::get('product', 'ProductController/GetProductList');
    Route::get('product/:id', 'ProductController/GetProductInfo')->pattern(['id' => '\d+']);
    Route::post('product', 'ProductController/CreateProduct');
    Route::put('product/:id', 'ProductController/UpdateProduct')->pattern(['id' => '\d+']);
    Route::delete('product/:id', 'ProductController/DeleteProduct')->pattern(['id' => '\d+']);
    // 订单管理
    Route::get('order', 'OrderController/GetOrderList');
    Route::get('order/:id', 'OrderController/GetOrderInfo')->pattern(['id' => '\d+']);
    Route::put('order/:id/cancel', 'OrderController/CancelOrder')->pattern(['id' => '\d+']);
    // 产品实例
    Route::get('host', 'HostController/GetHostList');
    Route::get('host/:id', 'HostController/GetHostInfo')->pattern(['id' => '\d+']);
    Route::put('host/:id/notes', 'HostController/UpdateHostNotes')->pattern(['id' => '\d+']);
    Route::post('host/:id/suspend', 'HostController/SuspendHost')
        ->pattern(['id' => '\d+'])
        ->middleware(\app\http\middleware\OperatePassword::class);
    Route::post('host/:id/unsuspend', 'HostController/UnsuspendHost')
        ->pattern(['id' => '\d+'])
        ->middleware(\app\http\middleware\OperatePassword::class);
    Route::post('host/:id/terminate', 'HostController/TerminateHost')
        ->pattern(['id' => '\d+'])
        ->middleware(\app\http\middleware\OperatePassword::class);
    // 服务器
    Route::get('server', 'ServerController/GetServerList');
    Route::get('server/:id', 'ServerController/GetServerInfo')->pattern(['id' => '\d+']);
    Route::post('server', 'ServerController/CreateServer');
    Route::put('server/:id', 'ServerController/UpdateServer')->pattern(['id' => '\d+']);
    Route::delete('server/:id', 'ServerController/DeleteServer')->pattern(['id' => '\d+']);
    // 服务器组
    Route::get('server_group', 'ServerGroupController/GetServerGroupList');
    Route::get('server_group/:id', 'ServerGroupController/GetServerGroupInfo')->pattern(['id' => '\d+']);
    Route::post('server_group', 'ServerGroupController/CreateServerGroup');
    Route::put('server_group/:id', 'ServerGroupController/UpdateServerGroup')->pattern(['id' => '\d+']);
    Route::delete('server_group/:id', 'ServerGroupController/DeleteServerGroup')->pattern(['id' => '\d+']);
    // 供应商
    Route::get('supplier', 'SupplierController/GetSupplierList');
    Route::get('supplier/:id', 'SupplierController/GetSupplierInfo')->pattern(['id' => '\d+']);
    Route::post('supplier', 'SupplierController/CreateSupplier');
    Route::put('supplier/:id', 'SupplierController/UpdateSupplier')->pattern(['id' => '\d+']);
    Route::delete('supplier/:id', 'SupplierController/DeleteSupplier')->pattern(['id' => '\d+']);
    // 上游
    Route::get('upstream/product', 'UpstreamProductController/GetUpstreamProductList');
    Route::get('upstream/order', 'UpstreamOrderController/GetUpstreamOrderList');
    Route::get('upstream/host', 'UpstreamHostController/GetUpstreamHostList');
    // 通知模板
    Route::get('notice/email', 'NoticeEmailController/GetEmailTemplateList');
    Route::get('notice/email/:id', 'NoticeEmailController/GetEmailTemplateInfo')->pattern(['id' => '\d+']);
    Route::put('notice/email/:id', 'NoticeEmailController/UpdateEmailTemplate')->pattern(['id' => '\d+']);
    Route::get('notice/sms', 'NoticeSmsController/GetSmsTemplateList');
    Route::get('notice/sms/:id', 'NoticeSmsController/GetSmsTemplateInfo')->pattern(['id' => '\d+']);
    Route::put('notice/sms/:id', 'NoticeSmsController/UpdateSmsTemplate')->pattern(['id' => '\d+']);
    Route::get('notice/setting', 'NoticeSettingController/GetNoticeSettingList');
    Route::put('notice/setting/:id', 'NoticeSettingController/UpdateNoticeSetting')->pattern(['id' => '\d+']);
    // 系统配置
    Route::get('configuration', 'ConfigurationController/getConfig');
    Route::put('configuration', 'ConfigurationController/updateConfig');
    Route::post('cache/clear', 'ConfigurationController/clearCache');
    Route::get('menu', 'ConfigurationController/menuList');
    Route::post('menu', 'ConfigurationController/menuCreate');
    Route::put('menu/:id', 'ConfigurationController/menuUpdate')->pattern(['id' => '\d+']);
    Route::delete('menu/:id', 'ConfigurationController/menuDelete')->pattern(['id' => '\d+']);
    // 插件
    Route::get('plugin', 'PluginController/GetPluginList');
    Route::get('plugin/:id', 'PluginController/GetPluginInfo')->pattern(['id' => '\d+']);
    Route::post('plugin/install', 'PluginController/InstallPlugin');
    Route::post('plugin/:id/uninstall', 'PluginController/UninstallPlugin')
        ->pattern(['id' => '\d+'])
        ->middleware(\app\http\middleware\OperatePassword::class);
    Route::put('plugin/:id/status', 'PluginController/TogglePluginStatus')->pattern(['id' => '\d+']);
    // 日志
    Route::get('log', 'LogController/GetLogList');
    Route::get('task', 'TaskController/GetTaskList');
    Route::post('task/:id/retry', 'TaskController/RetryTask')->pattern(['id' => '\d+']);
    // 组件
    Route::get('widget', 'WidgetController/GetWidgetList');
    Route::put('widget/order', 'WidgetController/UpdateWidgetOrder');
    // 网站管理
    Route::get('web_nav', 'WebNavController/GetWebNavList');
    Route::post('web_nav', 'WebNavController/CreateWebNav');
    Route::put('web_nav/:id', 'WebNavController/UpdateWebNav')->pattern(['id' => '\d+']);
    Route::delete('web_nav/:id', 'WebNavController/DeleteWebNav')->pattern(['id' => '\d+']);
    // 云服务器轮播图
    Route::get('cloud/server/banner', 'CloudServerBannerController/GetCloudServerBannerList');
    Route::post('cloud/server/banner', 'CloudServerBannerController/CreateCloudServerBanner');
    Route::put('cloud/server/banner/:id', 'CloudServerBannerController/UpdateCloudServerBanner')->pattern(['id' => '\d+']);
    Route::delete('cloud/server/banner/:id', 'CloudServerBannerController/DeleteCloudServerBanner')->pattern(['id' => '\d+']);
    Route::put('cloud/server/banner/:id/show', 'CloudServerBannerController/ToggleCloudServerBannerShow')->pattern(['id' => '\d+']);
    Route::put('cloud/server/banner/order', 'CloudServerBannerController/ReorderCloudServerBanner');
    // 首页轮播图
    Route::get('index/banner', 'IndexBannerController/GetIndexBannerList');
    Route::post('index/banner', 'IndexBannerController/CreateIndexBanner');
    Route::put('index/banner/:id', 'IndexBannerController/UpdateIndexBanner')->pattern(['id' => '\d+']);
    Route::delete('index/banner/:id', 'IndexBannerController/DeleteIndexBanner')->pattern(['id' => '\d+']);
    Route::put('index/banner/:id/show', 'IndexBannerController/ToggleIndexBannerShow')->pattern(['id' => '\d+']);
    Route::put('index/banner/order', 'IndexBannerController/ReorderIndexBanner');
    Route::get('seo', 'SeoController/GetSeoList');
    Route::post('seo', 'SeoController/CreateSeo');
    Route::put('seo/:id', 'SeoController/UpdateSeo')->pattern(['id' => '\d+']);
    Route::delete('seo/:id', 'SeoController/DeleteSeo')->pattern(['id' => '\d+']);
    // 公共
    Route::post('upload', 'CommonController/upload');
    Route::get('search', 'CommonController/globalSearch');
    Route::get('feedback', 'CommonController/feedbackList');
    Route::get('feedback_type', 'CommonController/feedbackTypeList');
})->middleware([
    \app\http\middleware\AdminAuth::class,
    \app\http\middleware\Cors::class,
    \app\http\middleware\Pagination::class,
    \app\http\middleware\ThrottleRepeat::class,
]);