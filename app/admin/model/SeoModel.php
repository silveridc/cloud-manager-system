<?php
namespace app\admin\model;

use think\Model;

/**
 * SEO模型
 * @desc SEO模型
 * @uses \app\admin\model\SeoModel
 */
class SeoModel extends Model
{
    protected $name = 'seo';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'          => 'int',
        'page'        => 'string',
        'title'       => 'string',
        'keywords'    => 'string',
        'description' => 'string',
    ];
}