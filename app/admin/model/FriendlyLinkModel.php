<?php
namespace app\admin\model;

use think\Model;

/**
 * 友情链接模型
 * @desc 友情链接模型
 * @uses \app\admin\model\FriendlyLinkModel
 */
class FriendlyLinkModel extends Model
{
    protected $name = 'friendly_link';

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