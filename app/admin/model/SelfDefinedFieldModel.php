<?php
namespace app\admin\model;

use think\Model;

/**
 * 自定义字段模型
 * @desc 自定义字段模型
 * @uses \app\admin\model\SelfDefinedFieldModel
 */
class SelfDefinedFieldModel extends Model
{
    protected $name = 'self_defined_field';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'field_type'  => 'string',
        'type'        => 'string',
        'product_id'  => 'int',
        'required'    => 'int',
        'options'     => 'string',
        'description' => 'string',
        'order'       => 'int',
    ];
}