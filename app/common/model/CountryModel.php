<?php
namespace app\common\model;

use think\Model;

/**
 * 国家模型
 * @desc 国家模型
 * @uses \app\common\model\CountryModel
 */
class CountryModel extends Model
{
    protected $name = 'country';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'         => 'int',
        'name_zh'    => 'string',
        'name_en'    => 'string',
        'phone_code' => 'string',
        'iso'        => 'string',
        'iso3'       => 'string',
        'order'      => 'int',
    ];
}