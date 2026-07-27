<?php
namespace app\admin\model;

use think\Model;

/**
 * 荣誉模型
 * @desc 荣誉模型
 * @uses \app\admin\model\HonorModel
 */
class HonorModel extends Model
{
    protected $name = 'honor';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'     => 'int',
        'name'   => 'string',
        'img'    => 'string',
        'order'  => 'int',
        'status' => 'int',
    ];
}