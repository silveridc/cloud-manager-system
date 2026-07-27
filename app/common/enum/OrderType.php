<?php

namespace app\common\enum;

/**
 * 订单类型
 */
enum OrderType: string
{
    case New = 'new';
    case Renew = 'renew';
    case Upgrade = 'upgrade';
    case Artificial = 'artificial';
    case Recharge = 'recharge';
    case OnDemand = 'on_demand';
    case ChangeBillingCycle = 'change_billing_cycle';

    /**
     * @title Label
     * @desc Label
     * @author zhaoyj
     * @version v1
     */
    public function label(): string
    {
        return match ($this) {
            self::New => lang('order_type_new'),
            self::Renew => lang('order_type_renew'),
            self::Upgrade => lang('order_type_upgrade'),
            self::Artificial => lang('order_type_artificial'),
            self::Recharge => lang('order_type_recharge'),
            self::OnDemand => lang('order_type_on_demand'),
            self::ChangeBillingCycle => lang('order_type_change_billing_cycle'),
        };
    }
}