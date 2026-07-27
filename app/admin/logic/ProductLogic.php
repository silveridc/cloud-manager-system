<?php
namespace app\admin\logic;

use app\common\entity\ProductEntity;
use app\common\model\ProductModel;
use app\common\model\HostModel;
use think\facade\Event;

/**
 * 产品业务逻辑
 * @desc 产品业务逻辑
 * @uses \app\admin\logic\ProductLogic
 */
class ProductLogic
{
    protected ProductEntity $entity;
    protected ProductModel $model;

    public function __construct(ProductEntity $entity, ProductModel $model)
    {
        $this->entity = $entity;
        $this->model = $model;
    }

    /**
     * 管理端产品列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function GetProductList(array $params): array
    {
        return $this->entity->listForAdmin($params);
    }

    /**
     * 客户端产品列表
     * @author zhaoyj
     * @version v1
     * @param array $params - 查询参数
     * @return array
     */
    public function getClientList(array $params): array
    {
        return $this->entity->listForClient($params);
    }

    /**
     * 产品详情
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品ID
     * @return array|null
     */
    public function GetProductInfo(int $id): ?array
    {
        return $this->entity->detail($id);
    }

    /**
     * 创建产品
     * @author zhaoyj
     * @version v1
     * @param array $params - 产品参数
     * @return int 产品ID
     * @throws \Throwable
     */
    public function CreateProduct(array $params): int
    {
        $product = new ProductModel();
        $product->startTrans();
        try {
            $product->save([
                'name' => $params['name'],
                'description' => $params['description'] ?? '',
                'type' => $params['type'] ?? '',
                'product_group_id' => $params['product_group_id'] ?? 0,
                'server_group_id' => $params['server_group_id'] ?? 0,
                'billing_cycle' => $params['billing_cycle'] ?? 'recurring_prepayment',
                'price' => $params['price'] ?? 0,
                'stock' => $params['stock'] ?? -1,
                'hidden' => $params['hidden'] ?? 0,
                'order' => $params['order'] ?? 0,
            ]);

            Event::trigger('after_product_create', ['product_id' => $product->id]);

            $product->commit();
            return (int)$product->id;
        } catch (\Throwable $e) {
            $product->rollback();
            throw $e;
        }
    }

    /**
     * 更新产品
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品ID
     * @param array $params - 更新参数
     * @return array|void
     */
    public function UpdateProduct(int $id, array $params)
    {
        $product = $this->model->find($id);
        if (!$product) {
            return [
                'status' => 404,
                'messages' => lang('product_not_found'),
                'time' => time(),
            ];
        }

        $allowFields = [
            'name', 'description', 'type', 'product_group_id',
            'server_group_id', 'billing_cycle', 'price', 'stock',
            'hidden', 'order',
        ];

        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($params[$field])) {
                $updateData[$field] = $params[$field];
            }
        }

        if (!empty($updateData)) {
            $product->save($updateData);
            Event::trigger('after_product_update', ['product_id' => $id]);
        }
    }

    /**
     * 删除产品
     * @author zhaoyj
     * @version v1
     * @param int $id - 产品ID
     * @return array|void
     */
    public function DeleteProduct(int $id)
    {
        $product = $this->model->find($id);
        if (!$product) {
            return [
                'status' => 404,
                'messages' => lang('product_not_found'),
                'time' => time(),
            ];
        }

        $HostModel = new HostModel();
        $hostCount = $HostModel
            ->where('product_id', $id)
            ->where('status', '<>', 'Deleted')
            ->count();

        if ($hostCount > 0) {
            return [
                'status' => 400,
                'messages' => lang('product_has_active_hosts'),
                'time' => time(),
            ];
        }

        $product->delete();
        Event::trigger('after_product_delete', ['product_id' => $id]);
    }
}