<?php
namespace app\common\model;
use app\admin\model\SupplierModel;
use think\Model;

/**
 * 上游产品模型
 * @desc 上游产品模型
 * @uses \app\common\model\UpstreamProductModel
 */
class UpstreamProductModel extends Model
{
    protected $name = 'upstream_product';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $schema = [
        'id'          => 'int',
        'supplier_id' => 'int',
        'product_id'  => 'int',
        'name'        => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
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
     * 关联所属产品
     * @author zhaoyj
     * @version v1
     * @return \think\model\relation\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}