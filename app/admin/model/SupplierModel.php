<?php
namespace app\admin\model;

use think\Model;

/**
 * 供应商模型
 * @desc 供应商模型
 * @uses \app\admin\model\SupplierModel
 */
class SupplierModel extends Model
{
    protected $name = 'supplier';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'url'         => 'string',
        'username'    => 'string',
        'token'       => 'string',
        'credit'      => 'decimal',
        'status'      => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    // 获取器

    /**
     * 获取器：格式化额度为两位小数
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始值
     * @return string - 格式化后的额度字符串
     */
    public function getCreditAttr($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }
}