<?php
namespace app\admin\model;

use think\Model;

/**
 * 合作伙伴模型
 * @desc 合作伙伴模型
 * @uses \app\admin\model\PartnerModel
 */
class PartnerModel extends Model
{
    protected $name = 'partner';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'     => 'int',
        'name'   => 'string',
        'url'    => 'string',
        'logo'   => 'string',
        'order'  => 'int',
        'status' => 'int',
    ];
}