<?php
// 多语言文件 zh-cn

return [
    // 通用
    'display_name' => '中文简体',//用于在语言切换下拉中显示
    'display_flag' => 'CN',//用于显示图片，使用国家代码大写
    'success_message' => '信息获取成功',
    'route_not_found' => '请求地址有误',
    'not_found' => '请求信息未找到',
    'update_success' => '修改成功',
    'delete_success' => '删除成功',
    'fail_message' => '请求异常',
    'unauthorized' => '未登录',
    'forbidden' => '无权限',
    'method_not_allowed' => '请求方法不允许',
    'data_not_found' => '数据不存在',
    'server_error' => '服务器内部错误',
    'invalid_param' => '参数错误',
    'invalid_qty' => '商品数量必须为 1 到 1000 的整数',
    'invalid_order_amount' => '订单金额无效',
    'insufficient_stock' => '库存不足',
    'insufficient_credit' => '余额不足',
    'repeat_request' => '请勿重复提交',
    'maintenance_mode' => '系统维护中，请稍后再试',

    // 认证
    'token_expired' => '登录已过期，请重新登录',
    'token_invalid' => 'Token 无效',
    'account_not_found' => '账号不存在',
    'account_disabled' => '账号已被禁用',
    'account_already_exists' => '账号已存在',
    'password_error' => '密码错误',
    'old_password_error' => '原密码错误',
    'password_changed' => '密码修改成功',
    'password_changed_relogin' => '密码已变更，请重新登录',
    'permission_denied' => '权限不足',
    'admin_not_found' => '管理员不存在',
    'admin_manage_denied' => '不能管理自己、超级管理员或同级/上级管理员',
    'target_permission_exceeds_actor' => '目标管理员拥有当前管理员不具备的权限，禁止接管或修改',
    'role_not_delegable' => '所选角色不可委派或层级不低于当前管理员',
    'role_permission_exceeds_actor' => '角色权限不能超出当前管理员拥有的权限',
    'cannot_manage_own_role' => '不能修改自己所属的角色',
    'role_has_protected_members' => '该角色关联了当前管理员自己或同级/上级管理员',
    'role_manage_denied' => '只能管理低于自己层级的角色',
    'role_level_too_high' => '新角色层级必须低于当前管理员层级',
    'invalid_role_level' => '角色层级必须是 1 到 9999 的整数',
    'invalid_delegable' => '可委派标记必须为 0 或 1',
    'delegated_role_must_remain_delegable' => '普通管理员创建或修改的角色必须保持可委派',
    'invalid_role_ids' => '角色 ID 必须是数组',
    'invalid_role_rules' => '角色权限必须是数组',

    // 操作密码
    'operate_password_required' => '请输入操作密码',
    'operate_password_error' => '操作密码错误',

    // 客户
    'client_not_found' => '客户不存在',
    'client_has_active_hosts' => '该客户名下仍有活跃产品实例，无法删除',

    // 产品
    'product_not_found' => '产品不存在',
    'product_has_active_hosts' => '该产品下仍有活跃实例，无法删除',

    // 订单
    'order_not_found' => '订单不存在',
    'order_cannot_cancel' => '该订单状态不允许取消',

    // 订单状态
    'order_status_unpaid' => '待支付',
    'order_status_paid' => '已支付',
    'order_status_cancelled' => '已取消',
    'order_status_refunded' => '已退款',
    'order_status_wait_upload' => '待上传凭证',
    'order_status_wait_review' => '待审核',
    'order_status_review_fail' => '审核失败',

    // 订单类型
    'order_type_new' => '新购',
    'order_type_renew' => '续费',
    'order_type_upgrade' => '升级',
    'order_type_artificial' => '人工',
    'order_type_recharge' => '充值',
    'order_type_on_demand' => '按量',
    'order_type_change_billing_cycle' => '变更周期',

    // 产品实例
    'host_not_found' => '产品实例不存在',
    'host_cannot_suspend' => '该实例状态不允许暂停',
    'host_not_suspended' => '该实例未处于暂停状态',
    'host_already_terminated' => '该实例已终止',

    // 产品实例状态
    'host_status_unpaid' => '待支付',
    'host_status_pending' => '开通中',
    'host_status_active' => '已开通',
    'host_status_suspended' => '已暂停',
    'host_status_deleted' => '已删除',
    'host_status_failed' => '开通失败',
    'host_status_cancelled' => '已取消',
    'host_status_grace' => '宽限期',
    'host_status_keep' => '保留中',

    // 计费周期
    'billing_cycle_free' => '免费',
    'billing_cycle_onetime' => '一次性',
    'billing_cycle_recurring_prepayment' => '周期预付',
    'billing_cycle_recurring_postpaid' => '周期后付',
    'billing_cycle_on_demand' => '按量计费',

    // 任务状态
    'task_status_wait' => '等待中',
    'task_status_exec' => '执行中',
    'task_status_finish' => '已完成',
    'task_status_failed' => '失败',
];
