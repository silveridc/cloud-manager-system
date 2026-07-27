<?php

namespace app\common\enum;

/**
 * 产品实例（Host）状态
 */
enum HostStatus: string
{
    case Unpaid = 'Unpaid';
    case Pending = 'Pending';
    case Active = 'Active';
    case Suspended = 'Suspended';
    case Deleted = 'Deleted';
    case Failed = 'Failed';
    case Cancelled = 'Cancelled';
    case Grace = 'Grace';
    case Keep = 'Keep';

    /**
     * @title Label
     * @desc Label
     * @author zhaoyj
     * @version v1
     */
    public function label(): string
    {
        return match ($this) {
            self::Unpaid => lang('host_status_unpaid'),
            self::Pending => lang('host_status_pending'),
            self::Active => lang('host_status_active'),
            self::Suspended => lang('host_status_suspended'),
            self::Deleted => lang('host_status_deleted'),
            self::Failed => lang('host_status_failed'),
            self::Cancelled => lang('host_status_cancelled'),
            self::Grace => lang('host_status_grace'),
            self::Keep => lang('host_status_keep'),
        };
    }
}