<?php
namespace app\common\model;

use think\Model;

/**
 * 客户模型
 * @desc 客户模型
 * @uses \app\common\model\ClientModel
 */
class ClientModel extends Model
{
    protected $name = 'client';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'               => 'int',
        'username'         => 'string',
        'email'            => 'string',
        'phone_code'       => 'string',
        'phone'            => 'string',
        'password'         => 'string',
        'operate_password' => 'string',
        'status'           => 'int',
        'credit'           => 'decimal',
        'company'          => 'string',
        'address'          => 'string',
        'language'         => 'string',
        'country_id'       => 'int',
        'notes'            => 'string',
        'last_login_time'  => 'int',
        'last_login_ip'    => 'string',
        'last_action_time' => 'int',
        'create_time'      => 'int',
        'update_time'      => 'int',
    ];

    // 隐藏敏感字段，防止 toArray() 泄露
    protected $hidden = ['password', 'operate_password'];

    // 关联：客户的订单

    /**
     * 关联客户的订单
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function orders()
    {
        return $this->hasMany(OrderModel::class, 'client_id');
    }

    // 关联：客户的产品实例

    /**
     * 关联客户的产品实例
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function hosts()
    {
        return $this->hasMany(HostModel::class, 'client_id');
    }

    // 关联：客户所属国家

    /**
     * 关联客户所属国家
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(CountryModel::class, 'country_id');
    }

    // 关联：客户登录记录

    /**
     * 关联客户登录记录
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function logins()
    {
        return $this->hasMany(ClientLoginModel::class, 'client_id');
    }

    // 关联：客户余额记录

    /**
     * 关联客户余额记录
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function credits()
    {
        return $this->hasMany(ClientCreditModel::class, 'client_id');
    }

    // 作用域：启用状态

    /**
     * 作用域：启用状态
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // 作用域：禁用状态

    /**
     * 作用域：禁用状态
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeDisabled($query)
    {
        return $query->where('status', 0);
    }

    // 获取器：格式化余额

    /**
     * 获取器：格式化客户余额
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始余额值
     * @return string - 格式化后的余额字符串
     */
    public function getCreditAttr($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }
}