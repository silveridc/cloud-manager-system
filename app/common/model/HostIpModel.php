<?php
namespace app\common\model;

use think\Model;

/**
 * 主机IP模型
 * @desc 主机IP模型
 * @uses \app\common\model\HostIpModel
 */
class HostIpModel extends Model
{
    protected $name = 'host_ip';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'          => 'int',
        'host_id'     => 'int',
        'ip'          => 'string',
        'subnet_mask' => 'string',
        'gateway'     => 'string',
    ];

    // 关联：所属产品实例

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