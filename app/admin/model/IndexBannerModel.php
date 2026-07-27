<?php
namespace app\admin\model;

use think\Model;

/**
 * 首页轮播图模型
 * @desc 首页轮播图模型
 * @uses \app\admin\model\IndexBannerModel
 */
class IndexBannerModel extends Model
{
    // 表名
    protected $name = 'index_banner';

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
