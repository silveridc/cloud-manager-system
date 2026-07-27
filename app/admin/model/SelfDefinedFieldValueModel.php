<?php
namespace app\admin\model;

use think\Model;
use think\db\Raw;
/**
 * 自定义字段值模型
 * @desc 自定义字段值模型
 * @uses \app\admin\model\SelfDefinedFieldValueModel
 */
class SelfDefinedFieldValueModel extends Model
{
    protected $name = 'self_defined_field_value';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'       => 'int',
        'field_id' => 'int',
        'type'     => 'string',
        'rel_id'   => 'int',
        'value'    => 'string',
    ];

    /**
     * 关联自定义字段
     * @author zhaoyj
     * @version v1
     * @param string|array|bool|Raw $field* @return \think\model\relation\BelongsTo
     */
    public function field(string|array|Raw|bool $field)
    {
        return $this->belongsTo(SelfDefinedFieldModel::class, 'field_id');
    }
}