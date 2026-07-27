<?php
// Language file en-us

return [
    // Common
    'success_message' => 'Success',
    'fail_message' => 'Operation failed',
    'unauthorized' => 'Unauthorized',
    'forbidden' => 'Forbidden',
    'not_found' => 'Not found',
    'route_not_found' => 'Request URL Error',
    'method_not_allowed' => 'Method not allowed',
    'data_not_found' => 'Data not found',
    'server_error' => 'Internal server error',
    'repeat_request' => 'Duplicate request, please try again later',
    'maintenance_mode' => 'System is under maintenance',

    // Auth
    'token_expired' => 'Session expired, please login again',
    'token_invalid' => 'Invalid token',
    'account_not_found' => 'Account not found',
    'account_disabled' => 'Account has been disabled',
    'account_already_exists' => 'Account already exists',
    'password_error' => 'Incorrect password',
    'old_password_error' => 'Current password is incorrect',
    'password_changed' => 'Password changed successfully',
    'password_changed_relogin' => 'Password changed, please login again',
    'permission_denied' => 'Permission denied',
    'admin_not_found' => 'Admin not found',

    // Operate password
    'operate_password_required' => 'Operation password required',
    'operate_password_error' => 'Incorrect operation password',

    // Client
    'client_not_found' => 'Client not found',
    'client_has_active_hosts' => 'Client has active services, cannot delete',

    // Product
    'product_not_found' => 'Product not found',
    'product_has_active_hosts' => 'Product has active instances, cannot delete',

    // Order
    'order_not_found' => 'Order not found',
    'order_cannot_cancel' => 'Order cannot be cancelled in current status',

    // Order status
    'order_status_unpaid' => 'Unpaid',
    'order_status_paid' => 'Paid',
    'order_status_cancelled' => 'Cancelled',
    'order_status_refunded' => 'Refunded',
    'order_status_wait_upload' => 'Awaiting voucher',
    'order_status_wait_review' => 'Under review',
    'order_status_review_fail' => 'Review failed',

    // Order type
    'order_type_new' => 'New',
    'order_type_renew' => 'Renew',
    'order_type_upgrade' => 'Upgrade',
    'order_type_artificial' => 'Manual',
    'order_type_recharge' => 'Recharge',
    'order_type_on_demand' => 'On-demand',
    'order_type_change_billing_cycle' => 'Change cycle',

    // Host
    'host_not_found' => 'Service instance not found',
    'host_cannot_suspend' => 'Cannot suspend in current status',
    'host_not_suspended' => 'Service is not suspended',
    'host_already_terminated' => 'Service already terminated',

    // Host status
    'host_status_unpaid' => 'Unpaid',
    'host_status_pending' => 'Pending',
    'host_status_active' => 'Active',
    'host_status_suspended' => 'Suspended',
    'host_status_deleted' => 'Terminated',
    'host_status_failed' => 'Failed',
    'host_status_cancelled' => 'Cancelled',
    'host_status_grace' => 'Grace period',
    'host_status_keep' => 'Retained',

    // Billing cycle
    'billing_cycle_free' => 'Free',
    'billing_cycle_onetime' => 'One-time',
    'billing_cycle_recurring_prepayment' => 'Recurring prepaid',
    'billing_cycle_recurring_postpaid' => 'Recurring postpaid',
    'billing_cycle_on_demand' => 'On-demand',

    // Task status
    'task_status_wait' => 'Waiting',
    'task_status_exec' => 'Running',
    'task_status_finish' => 'Completed',
    'task_status_failed' => 'Failed',
];
