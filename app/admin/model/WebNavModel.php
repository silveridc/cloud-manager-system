<?php
namespace app\admin\model;

use think\Model;

/**
 * 网站导航模型
 * @desc 网站导航模型
 * @uses \app\admin\model\WebNavModel
 */
class WebNavModel extends Model
{
    protected $name = 'web_nav';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'        => 'int',
        'name'      => 'string',
        'url'       => 'string',
        'target'    => 'string',
        'parent_id' => 'int',
        'order'     => 'int',
        'status'    => 'int',
    ];

    /**
     * 关联子导航列表
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * 关联父级导航
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}