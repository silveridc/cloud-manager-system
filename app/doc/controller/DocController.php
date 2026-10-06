<?php
namespace app\doc\controller;

use think\facade\Config;
use app\BaseController;
use think\facade\View;
use app\doc\logic\DocLogic as Doc;
class DocController extends BaseController
{
    /**
     * @var \think\Request Request实例
     */
    protected $request;

    /**
     * @var Doc
     */
    protected $doc;

    /**
     * @var array 资源类型
     */
    protected $mimeType = [
        'xml'  => 'application/xml,text/xml,application/x-xml',
        'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js'   => 'text/javascript,application/javascript,application/x-javascript',
        'css'  => 'text/css',
        'rss'  => 'application/rss+xml',
        'yaml' => 'application/x-yaml,text/yaml',
        'atom' => 'application/atom+xml',
        'pdf'  => 'application/pdf',
        'text' => 'text/plain',
        'png'  => 'image/png',
        'jpg'  => 'image/jpg,image/jpeg,image/pjpeg',
        'gif'  => 'image/gif',
        'csv'  => 'text/csv',
        'html' => 'text/html,application/xhtml+xml,*/*',
    ];

    public $static_path = '/doc-static/';

    public function initialize(){
        parent::initialize();
        $this->doc = new Doc((array)Config::get('doc'));
        View::assign('title', Config::get("doc.title"));
        View::assign('version', Config::get("doc.version"));
        View::assign('copyright', Config::get("doc.copyright"));
        if(Config::get("doc.static_path", '')){
            $this->static_path = Config::get("doc.static_path");
        }
        View::assign('static', $this->static_path);
    }

    /**
     * 文档首页
     * @return Response
     */
    public function index()
    {
        View::assign('root', $this->request->root());
        if(!$this->checkLogin()){
            return redirect('pass');
        }
        return view('/index', ['doc' => json_encode(
            (string)$this->request->get('name', ''),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        )]);
    }

    /**
     * 文档搜素
     * @return \think\Response|\think\response\View
     */
    public function search()
    {
        if(!$this->checkLogin()) {
            return $this->request->isAjax() ? json(['status' => 401, 'message' => lang('unauthorized')], 401) : redirect('pass');
        }

        if($this->request->isAjax())
        {
            $data = $this->doc->searchList($this->request->get('query'));
            return json($data);
        }
        else
        {
            $module = $this->doc->getModuleList();
            View::assign('root', $this->request->root());
            return view('/search', ['module' => $module]);
        }
    }

    /**
     * 设置目录树及图标
     * @param $actions
     * @param int $num
     * @return mixed
     */
    protected function setIcon($actions, $num = 1)
    {
        foreach ($actions as $key=>$moudel){
            if(isset($moudel['actions'])){
                $actions[$key]['iconClose'] = $this->static_path."/js/zTree_v3/img/zt-folder.png";
                $actions[$key]['iconOpen'] = $this->static_path."/js/zTree_v3/img/zt-folder-o.png";
                $actions[$key]['open'] = true;
                $actions[$key]['isParent'] = true;
                $actions[$key]['actions'] = $this->setIcon($moudel['actions'], $num = 1);
            }else{
                $actions[$key]['icon'] = $this->static_path."/js/zTree_v3/img/zt-file.png";
                $actions[$key]['isParent'] = false;
                $actions[$key]['isText'] = true;
            }
        }
        return $actions;
    }

    /**
     * 接口列表
     */
    public function getList()
    {
        if (!$this->checkLogin()) {
            return json(['status' => 401, 'message' => lang('unauthorized')], 401);
        }

        $list = $this->doc->getList();
        $list = $this->setIcon($list);
        return json(['firstId'=>'', 'list'=>$list]);
    }

    /**
     * 接口详情
     * @return mixed
     */
    public function getInfo()
    {
        if($this->checkLogin() == false){
            return redirect('pass');
        }
        $name = (string)$this->request->get('name', '');
        if (!str_contains($name, '::')) {
            return json(['status' => 400, 'message' => lang('invalid_param')], 400);
        }

        [$class, $action] = explode('::', $name, 2);
        if (!$this->isDocumentedAction($class, $action)) {
            return json(['status' => 404, 'message' => lang('not_found')], 404);
        }

        $action_doc = $this->doc->getInfo($class, $action);
        if($action_doc)
        {
            $return = nl2br(htmlspecialchars(
                str_replace(['&nbsp;', '<br>', '<br/>', '<br />'], [' ', "\n", "\n", "\n"], $this->doc->formatReturn($action_doc)),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ));
            $action_doc['header'] = isset($action_doc['header']) ? array_merge($this->doc->__get('public_header'), $action_doc['header']) : [];
            $action_doc['param'] = isset($action_doc['param']) ? array_merge($this->doc->__get('public_param'), $action_doc['param']) : [];
            
            // 此时底层送上来的已经是洗白后的param二维数组，直接循环绝不报 undefined 
            $curl_code = 'curl --location --request '.($action_doc['method'] ?? 'GET');
            $params = [];
            foreach ($action_doc['param'] as $param){
                $params[$param['name']] = $param['default'] ?? '';
            }
            $curl_code .= ' \''.$this->request->root().($action_doc["url"] ?? '').(count($params) > 0 ? '?'.http_build_query($params) : '').'\' ';
            foreach ($action_doc['header'] as $header){
                $curl_code .= '--header \''.$header['name'].':\'';
            }
            View::assign('root', $this->request->root());
            return view('/info', ['doc'=>$action_doc, 'return'=>$return, 'curl_code' => $curl_code]);
        }
    }

    private function isDocumentedAction(string $class, string $action): bool
    {
        if (!in_array($class, (array)$this->doc->__get('controller'), true) || !class_exists($class)) {
            return false;
        }

        $reflection = new \ReflectionClass($class);
        return $reflection->hasMethod($action) && $reflection->getMethod($action)->isPublic();
    }

    /**
     * 验证密码
     * @return bool
     */
    protected function checkLogin()
    {
        $pass = $this->doc->__get("password");
        if (!$pass) {
            return true;
        }

        return $this->request->session('apidoc-pass') === sha1($pass);
    }

    /**
     * 输入密码
     * @return string
     */
    public function pass()
    {
        View::assign('root', $this->request->root());
        return view('/pass');
    }

    /**
     * 登录
     * @return string
     */
    public function login()
    {
        $pass = $this->doc->__get("password");
        if($pass && $this->request->param('pass') === $pass){
            $this->request->session()->regenerate(true);
            $this->request->session()->set('apidoc-pass', sha1($pass));
            $data = ['status' => '200', 'message' => '登录成功'];
        }else if(!$pass){
            $data = ['status' => '200', 'message' => '登录成功'];
        }else{
            $data = ['status' => '300', 'message' => '密码错误'];
        }
        return json($data);
    }

    /**
     * 接口访问测试
     * @return \think\Response
     */
    public function debug()
    {
        $data = [];
        $api_url = '';
        $res = [
            'status' => '404',
            'meaasge' => '在线调试功能已禁用',
            'result' => '',
        ];
        return json($res);
    }

    /**
     * 在线调试已禁用，保留方法以兼容旧调用。
     */
    private function http_request($url, $cookie, $data = array(), $method = array(), $headers = array()){
        return '';
    }

}