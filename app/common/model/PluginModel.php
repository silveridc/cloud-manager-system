<?php
namespace app\common\model;

use think\Model;

/**
 * 插件模型
 * @desc 插件模型
 * @uses \app\common\model\PluginModel
 */
class PluginModel extends Model
{
    protected $name = 'plugin';

    protected $autoWriteTimestamp = false;

    protected $schema = [
        'id'          => 'int',
        'type'        => 'string',
        'name'        => 'string',
        'title'       => 'string',
        'version'     => 'string',
        'author'      => 'string',
        'description' => 'string',
        'status'      => 'int',
        'config'      => 'string',
    ];

    // 作用域：已启用

    /**
     * 作用域：筛选已启用的插件
     * @author zhaoyj
     * @version v1
     * @param \think\db\Query $query - 查询对象
     * @return \think\db\Query
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }

    // 获取器：JSON 配置解码

    /**
     * 获取器：将JSON配置字符串解码为数组
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始值
     * @return array - 解码后的配置数组
     */
    public function getConfigAttr($value): array
    {
        return $value ? json_decode($value, true) : [];
    }

    // 修改器：JSON 配置编码

    /**
     * 修改器：将配置数组编码为JSON字符串
     * @author zhaoyj
     * @version v1
     * @param mixed $value - 原始值（数组或字符串）
     * @return string - 编码后的JSON字符串
     */
    public function setConfigAttr($value): string
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string)$value;
    }
}