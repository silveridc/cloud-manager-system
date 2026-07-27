<?php
namespace app\admin\model;

use think\Model;

/**
 * 菜单模型
 * @desc 菜单模型
 * @uses \app\admin\model\MenuModel
 */
class MenuModel extends Model
{
    protected $name = 'menu';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'        => 'int',
        'type'      => 'string',
        'name'      => 'string',
        'icon'      => 'string',
        'url'       => 'string',
        'order'     => 'int',
        'parent_id' => 'int',
        'status'    => 'int',
    ];

    /**
     * 作用域：筛选管理端菜单
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeAdmin($query)
    {
        return $query->where('type', 'admin');
    }

    /**
     * 作用域：筛选客户端菜单
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeHome($query)
    {
        return $query->where('type', 'home');
    }
}