<?php
namespace app\admin\model;

use think\Model;

/**
 * 云服务器轮播图模型
 * @desc 云服务器轮播图模型
 * @uses \app\admin\model\CloudServerBannerModel
 */
class CloudServerBannerModel extends Model
{
    // 表名
    protected $name = 'cloud_server_banner';

    protected $autoWriteTimestamp = false;

    // 设置字段信息
    protected $schema = [
        'id'            => 'int',
        'img'           => 'string',
        'url'           => 'string',
        'start_time'    => 'int',
        'end_time'      => 'int',
        'show'          => 'int',
        'notes'         => 'string',
        'order'         => 'int',
        'create_time'   => 'int',
        'update_time'   => 'int',
    ];
}
