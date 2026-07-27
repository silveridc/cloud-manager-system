<?php
namespace app\admin\controller;

use app\admin\logic\CacheLogic;
use app\admin\logic\ConfigurationLogic;
use app\admin\logic\MenuLogic;

/**
 * 系统配置控制器
 * @desc 系统配置控制器
 * @uses \app\admin\controller\ConfigurationController
 */
class ConfigurationController extends AdminBaseController
{
    protected ConfigurationLogic $configLogic;
    protected CacheLogic $cacheLogic;
    protected MenuLogic $menuLogic;

    protected function initialize()
    {
        parent::initialize();
        $this->configLogic = app(ConfigurationLogic::class);
        $this->cacheLogic = app(CacheLogic::class);
        $this->menuLogic = app(MenuLogic::class);
    }

    /**
     * @title Get Config
     * @desc Get Config
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/configuration/:id
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function getConfig()
    {
        $group = $this->request->param('group', '');
        if ($group) {
            $data = $this->configLogic->getGroup($group);
        } else {
            $data = $this->configLogic->getAll();
        }
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Update Config
     * @desc Update Config
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/configuration/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function updateConfig()
    {
        $params = $this->request->param();

        // 过滤框架内部路由参数及危险 key
        $dangerousKeys = ['page', 'limit', 'sort', 's', 'group', 'controller', 'action', 'method'];
        foreach ($dangerousKeys as $key) {
            unset($params[$key]);
        }

        // 仅保留数据库中已存在的配置项（白名单校验）
        if (!empty($params)) {
            $validKeys = \think\facade\Db::name('configuration')
                ->whereIn('setting', array_keys($params))
                ->column('setting');
            $params = array_intersect_key($params, array_flip($validKeys));
        }

        if (!empty($params)) {
            $this->configLogic->batchUpdate($params);
        }

        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Clear Cache
     * @desc Clear Cache
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/cache/clear
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function clearCache()
    {
        $type = $this->request->param('type', 'all');
        match ($type) {
            'config' => $this->cacheLogic->clearConfig(),
            'route' => $this->cacheLogic->clearRoute(),
            'view' => $this->cacheLogic->clearView(),
            default => $this->cacheLogic->clearAll(),
        };
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Menu List
     * @desc Menu List
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/menu
     * @method get
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function menuList()
    {
        $type = $this->request->param('type', 'admin');
        $data = $type === 'home'
            ? $this->menuLogic->getHomeMenu()
            : $this->menuLogic->getAdminMenu();
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'data' => $data,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Menu Create
     * @desc Menu Create
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/menu
     * @method post
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function menuCreate()
    {
        $params = $this->request->param();
        $id = $this->menuLogic->create($params);
        $result = [
            'status' => 200,
            'messages' => lang('success_message'),
            'id' => $id,
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Menu Update
     * @desc Menu Update
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/menu/:id
     * @method put
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function menuUpdate(int $id)
    {
        $params = $this->request->param();
        $this->menuLogic->update($id, $params);
        $result = [
            'status' => 200,
            'messages' => lang('update_success'),
            'time' => time(),
        ];
        return json($result);
    }

    /**
     * @title Menu Delete
     * @desc Menu Delete
     * @author zhaoyj
     * @version v1
     * @url /admin/v1/menu/:id
     * @method delete
     * @return json
     * @return int status - 状态码,200ok
     * @return string messages - 提示信息
     * @return int time - 时间戳
     */
    public function menuDelete(int $id)
    {
        $this->menuLogic->delete($id);
        $result = [
            'status' => 200,
            'messages' => lang('delete_success'),
            'time' => time(),
        ];
        return json($result);
    }
}