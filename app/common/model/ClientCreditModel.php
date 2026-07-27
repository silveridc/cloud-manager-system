<?php
namespace app\common\model;

use think\Model;

/**
 * 客户余额模型
 * @desc 客户余额模型
 * @uses \app\common\model\ClientCreditModel
 */
class ClientCreditModel extends Model
{
    protected $name = 'client_credit';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'client_id'   => 'int',
        'type'        => 'string',
        'amount'      => 'decimal',
        'balance'     => 'decimal',
        'notes'       => 'string',
        'create_time' => 'int',
    ];

    // 关联：所属客户

    /**
     * 关联所属客户
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    // 获取器

    /**
     * 获取器：格式化金额为两位小数字符串
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始值
     * @return string - 格式化后的金额字符串
     */
    public function getAmountAttr($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }
}