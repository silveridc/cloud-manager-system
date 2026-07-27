<?php
namespace app\admin\logic;

use app\admin\model\SelfDefinedFieldModel;
use app\admin\model\SelfDefinedFieldValueModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 自定义字段逻辑
 * @desc 自定义字段逻辑
 * @uses \app\admin\logic\SelfDefinedFieldLogic
 */
class SelfDefinedFieldLogic
{
    /**
     * 获取自定义字段列表
     * @author zhaoyj
     * @version v1
     * @param array $params .limit - 每页数量
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList(array $params): array
    {
        $SelfDefinedFieldModel = new SelfDefinedFieldModel();
        $query = $SelfDefinedFieldModel;

        if (!empty($params['type'])) {
            $query = $query->where('type', $params['type']);
        }
        if (!empty($params['product_id'])) {
            $query = $query->where('product_id', (int)$params['product_id']);
        }

        $count = (clone $query)->count();
        $list = $query
            ->order('order', 'asc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 50)
            ->select()->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 创建自定义字段
     * @author zhaoyj
     * @version v1
     * @param array $params .order - 排序值（默认0）
     * @return int - 新创建的字段ID
     */
    public function create(array $params): int
    {
        $SelfDefinedFieldModel = new SelfDefinedFieldModel();
        return (int)$SelfDefinedFieldModel->insertGetId([
            'name' => $params['name'],
            'field_type' => $params['field_type'] ?? 'text',
            'type' => $params['type'] ?? 'host',
            'product_id' => $params['product_id'] ?? 0,
            'required' => $params['required'] ?? 0,
            'options' => $params['options'] ?? '',
            'description' => $params['description'] ?? '',
            'order' => $params['order'] ?? 0,
        ]);
    }

    /**
     * 更新自定义字段
     * @author zhaoyj
     * @version v1
     * @param int $id - 字段ID
     * @param array $params .order - 排序值
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $params)
    {
        $SelfDefinedFieldModel = new SelfDefinedFieldModel();
        $field = $SelfDefinedFieldModel->where('id', $id)->find();
        if (!$field) {
            return [
                'status' => 404,
                'messages' => lang('data_not_found'),
                'time' => time(),
            ];
        }

        $allowFields = ['name', 'field_type', 'type', 'product_id', 'required', 'options', 'description', 'order'];
        $updateData = [];
        foreach ($allowFields as $f) {
            if (isset($params[$f])) {
                $updateData[$f] = $params[$f];
            }
        }

        if ($updateData) {
            $SelfDefinedFieldModel->where('id', $id)->update($updateData);
        }
    }

    /**
     * 删除自定义字段及其关联值
     * @author zhaoyj
     * @version v1
     * @param int $id - 字段ID
     * @return void
     */
    public function delete(int $id): void
    {
        $SelfDefinedFieldModel = new SelfDefinedFieldModel();
        $SelfDefinedFieldValueModel = new SelfDefinedFieldValueModel();
        $SelfDefinedFieldModel->where('id', $id)->delete();
        $SelfDefinedFieldValueModel->where('field_id', $id)->delete();
    }

    /**
     * 获取指定实体的自定义字段值
     * @author zhaoyj
     * @version v1
     * @param string $type - 实体类型（如host）
     * @param int $relId - 关联实体ID
     * @return array
     * @return string list.name - 字段名称
     * @return string list.field_type - 字段类型
     * @return string list.value - 字段值
     * @return int list.field_id - 字段ID
     */
    public function getValues(string $type, int $relId): array
    {
        $SelfDefinedFieldValueModel = new SelfDefinedFieldValueModel();
        return $SelfDefinedFieldValueModel
            ->alias('v')
            ->leftJoin('self_defined_field f', 'f.id = v.field_id')
            ->where('v.type', $type)
            ->where('v.rel_id', $relId)
            ->field('f.name, f.field_type, v.value, v.field_id')
            ->select()->toArray();
    }

    /**
     * 保存自定义字段值（先删除旧值再批量插入新值）
     * @author zhaoyj
     * @version v1
     * @param string $type - 实体类型（如host）
     * @param int $relId - 关联实体ID
     * @param array $fields - 字段值映射，键为字段ID，值为字段值
     * @return void
     */
    public function saveValues(string $type, int $relId, array $fields): void
    {
        $SelfDefinedFieldValueModel = new SelfDefinedFieldValueModel();
        // 批量删除旧值再插入
        $SelfDefinedFieldValueModel
            ->where('type', $type)
            ->where('rel_id', $relId)
            ->delete();

        $data = [];
        foreach ($fields as $fieldId => $value) {
            $data[] = [
                'field_id' => (int)$fieldId,
                'type' => $type,
                'rel_id' => $relId,
                'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string)$value,
            ];
        }

        if ($data) {
            (new SelfDefinedFieldValueModel())->insertAll($data);
        }
    }
}