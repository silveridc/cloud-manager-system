<?php
namespace app\admin\model;

use think\Model;

/**
 * 底部导航分组模型
 * @desc 底部导航分组模型
 * @uses \app\admin\model\BottomBarGroupModel
 */
class BottomBarGroupModel extends Model
{
    protected $name = 'bottom_bar_group';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'    => 'int',
        'name'  => 'string',
        'order' => 'int',
    ];

    /**
     * 关联分组下的导航项（一对多）
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\HasMany
     */
    public function navs()
    {
        return $this->hasMany(BottomBarNavModel::class, 'group_id');
    }
}