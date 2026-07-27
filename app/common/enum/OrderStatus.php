<?php

namespace app\common\enum;

/**
 * 订单状态
 */
enum OrderStatus: string
{
    case Unpaid = 'Unpaid';
    case Paid = 'Paid';
    case Cancelled = 'Cancelled';
    case Refunded = 'Refunded';
    case WaitUpload = 'WaitUpload';
    case WaitReview = 'WaitReview';
    case ReviewFail = 'ReviewFail';

    /**
     * @title Label
     * @desc Label
     * @author zhaoyj
     * @version v1
     */
    public function label(): string
    {
        return match ($this) {
            self::Unpaid => lang('order_status_unpaid'),
            self::Paid => lang('order_status_paid'),
            self::Cancelled => lang('order_status_cancelled'),
            self::Refunded => lang('order_status_refunded'),
            self::WaitUpload => lang('order_status_wait_upload'),
            self::WaitReview => lang('order_status_wait_review'),
            self::ReviewFail => lang('order_status_review_fail'),
        };
    }
}