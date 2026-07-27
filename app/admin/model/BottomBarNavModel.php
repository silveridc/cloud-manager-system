<?php
namespace app\admin\model;

use think\Model;

/**
 * 底部导航模型
 * @desc 底部导航模型
 * @uses \app\admin\model\BottomBarNavModel
 */
class BottomBarNavModel extends Model
{
    protected $name = 'bottom_bar_nav';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'       => 'int',
        'group_id' => 'int',
        'name'     => 'string',
        'url'      => 'string',
        'order'    => 'int',
    ];

    /**
     * 关联所属底部导航分组
     * @author zhaoyj
     * @version v1
     * @param $group* @return \think\model\relation\BelongsTo
     */
    public function group($group)
    {
        return $this->belongsTo(BottomBarGroupModel::class, 'group_id');
    }
}