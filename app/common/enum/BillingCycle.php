<?php

namespace app\common\enum;

/**
 * 计费周期
 */
enum BillingCycle: string
{
    case Free = 'free';
    case OneTime = 'onetime';
    case RecurringPrepayment = 'recurring_prepayment';
    case RecurringPostpaid = 'recurring_postpaid';
    case OnDemand = 'on_demand';

    /**
     * 获取计费周期标签
     * @author zhaoyj
     * @version v1
     * @return string - 多语言标签
     */
    public function label(): string
    {
        return match ($this) {
            self::Free => lang('billing_cycle_free'),
            self::OneTime => lang('billing_cycle_onetime'),
            self::RecurringPrepayment => lang('billing_cycle_recurring_prepayment'),
            self::RecurringPostpaid => lang('billing_cycle_recurring_postpaid'),
            self::OnDemand => lang('billing_cycle_on_demand'),
        };
    }
}