<?php
namespace app\common\model;

use think\Model;
use app\admin\model\SupplierModel;
/**
 * 上游主机模型
 * @desc 上游主机模型
 * @uses \app\common\model\UpstreamHostModel
 */
class UpstreamHostModel extends Model
{
    protected $name = 'upstream_host';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'supplier_id' => 'int',
        'host_id'     => 'int',
        'create_time' => 'int',
    ];

    /**
     * 关联所属供应商
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id');
    }

    /**
     * 关联所属产品实例
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo(HostModel::class, 'host_id');
    }
}