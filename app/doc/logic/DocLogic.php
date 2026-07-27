<?php
namespace app\doc\logic;

class DocLogic
{
    protected  $config = [
        'title'=>'APi接口文档',
        'version'=>'1.0.0',
        'copyright'=>'Powered By Zhangweiwei',
        'controller' => [],
        'filter_method'=>['_empty'],
        'return_format' => [
            'status' => "200/300/301/302",
            'message' => "提示信息",
        ]
    ];

    public function __construct($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 使用 $this->name 获取配置
     * @access public
     * @param  string $name 配置名称
     * @return mixed    配置值
     */
    public function __get($name)
    {
        return $this->config[$name] ?? null;
    }

    /**
     * 设置验证码配置
     * @access public
     * @param  string $name  配置名称
     * @param  string $value 配置值
     * @return void
     */
    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 检查配置
     * @access public
     * @param  string $name 配置名称
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    /**
     * 获取接口列表
     * @return array
     */
    public function getList()
    {
        $controller = $this->config['controller'];
        $list = [];
        foreach ($controller as $class)
        {
            if(class_exists($class))
            {
                $module = [];
                $reflection = new \ReflectionClass($class);
                $doc_str = $reflection->getDocComment();
                $doc = new DocParserLogic();
                $class_doc = $doc->parse($doc_str);
                if (!isset($module['group']) || empty($module['group'])) {
                    if (isset($module['desc']) && !empty($module['desc'])) {
                        $module['group'] = $module['desc']; // 如果有 @desc，用描述当组名
                } elseif (isset($module['description']) && !empty($module['description'])) {
                    $module['group'] = $module['description']; 
                } else {
                    // 如果啥注释都没写，直接截取类名，比如 AdminController
                    $classParts = explode('\\', $class);
                    $module['group'] = end($classParts); 
                }
}
// 顺便确保 title 字段也有值，老包很喜欢看 title
if (!isset($module['title']) || empty($module['title'])) {
    $module['title'] = $module['group'];
}
                $module =  $class_doc;
                $module['class'] = $class;
                $method = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                $filter_method = array_merge(['__construct'], $this->config['filter_method']);
                $module['actions'] = [];
                foreach ($method as $action){
                    if(!in_array($action->name, $filter_method))
                    {
                        $doc = new DocParserLogic();
                        $doc_str = $action->getDocComment();
                        if($doc_str)
                        {
                            $action_doc = $doc->parse($doc_str);
                            $action_doc['name'] = $class."::".$action->name;
                            $action_doc['param'] = $this->cleanCommentArray($action_doc['param'] ?? []);
                            $action_doc['header'] = $this->cleanCommentArray($action_doc['header'] ?? [], true);
                            if(array_key_exists('title', $action_doc)){
                                if(array_key_exists('module', $action_doc)){
                                    $key = array_search($action_doc['module'], array_column($module['actions'], 'title'));
                                    if($key === false){
                                        $action = $module;
                                        $action['title'] = $action_doc['module'];
                                        $action['module'] = $action_doc['module'];
                                        $action['actions'] = [];
                                        array_push($action['actions'], $action_doc);
                                        array_push($module['actions'], $action);
                                    }else{
                                        array_push($module['actions'][$key]['actions'], $action_doc);
                                    }
                                }else{
                                    array_push($module['actions'], $action_doc);
                                }
                            }
                        }
                    }
                }
                if(array_key_exists('group', $module)){
                    $key = array_search($module['group'], array_column($list, 'title'));
                    if($key === false){ //创建分组
                        $floder = [
                            'title' => $module['group'],
                            'description' => '',
                            'package' => '',
                            'class' => '',
                            'actions' => []
                        ];
                        array_push($floder['actions'], $module);
                        array_push($list, $floder);
                    }else{
                        array_push($list[$key]['actions'], $module);
                    }
                }else{
                    array_push($list, $module);
                }
            }
        }
        return $list;
    }

    /**
     * 文档目录列表
     * @return array
     */
    public function getModuleList()
    {
        $controller = $this->config['controller'];
        $list = [];
        foreach ($controller as $class) {
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $doc_str = $reflection->getDocComment();
                $doc = new DocParserLogic();
                $class_doc = $doc->parse($doc_str);
                if(array_key_exists('group', $class_doc)){
                    $key = array_search($class_doc['group'], array_column($list, 'title'));
                    if($key === false){ //创建分组
                        $floder = [
                            'title' => $class_doc['group'],
                            'children' => []
                        ];
                        array_push($floder['children'], $class_doc);
                        array_push($list, $floder);
                    }
                    else
                    {
                        array_push($list[$key]['children'], $class_doc);
                    }
                }else{
                    array_push($list, $class_doc);
                }
            }
        }
        return $list;
    }

    /**
     * 获取类中指导方法注释详情
     * @param $class
     * @param $action
     * @return array
     */
    public function getInfo($class, $action)
    {
        $action_doc = [];
        if($class && class_exists($class)){
            $reflection = new \ReflectionClass($class);
            $doc_str = $reflection->getDocComment();
            $doc = new DocParserLogic();
            $class_doc = $doc->parse($doc_str);
            $class_doc['header'] = isset($class_doc['header'])? $class_doc['header'] : [];
            $class_doc['param'] = isset($class_doc['param']) ? $class_doc['param'] : [];
            if($reflection->hasMethod($action)) {
                $method = $reflection->getMethod($action);
                $doc = new DocParserLogic();
                $action_doc = $doc->parse($method->getDocComment());
                $action_doc['name'] = $class."::".$method->name;
                $action_doc['header'] = isset($action_doc['header']) ? array_merge($class_doc['header'], $action_doc['header']) : $class_doc['header'];
                $action_doc['param'] = isset($action_doc['param']) ? array_merge($class_doc['param'], $action_doc['param']) : $class_doc['param'];
                $action_doc['param'] = $this->cleanCommentArray($action_doc['param']);
                $action_doc['header'] = $this->cleanCommentArray($action_doc['header'], true);
            }
        }
        return $action_doc;
    }

    /**
     * 文档列表搜素
     * @param string $keyword
     * @return array
     */
    public function searchList($keyword = "")
    {
        $controller = $this->config['controller'];
        $list = [];
        foreach ($controller as $class)
        {
            if(class_exists($class))
            {
                $reflection = new \ReflectionClass($class);
                $method = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                $filter_method = array_merge(['__construct'], $this->config['filter_method']);
                foreach ($method as $action){
                    if(!in_array($action->name, $filter_method))
                    {
                        $doc = new DocParserLogic();
                        $doc_str = $action->getDocComment();
                        if($doc_str)
                        {
                            $action_doc = $doc->parse($doc_str);
                            $action_doc['name'] = $class."::".$action->name;
                            if((isset($action_doc['title']) && strpos($action_doc['title'], $keyword) !== false)
                                    || (isset($action_doc['description']) && strpos($action_doc['description'], $keyword) !== false)
                                    || (isset($action_doc['author']) && strpos($action_doc['author'], $keyword) !== false)
                                    || (isset($action_doc['url'])  && strpos($action_doc['url'], $keyword) !== false))
                            {
                                array_push($list, $action_doc);
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 格式化数组为json字符串-用于格式显示
     * @param array $doc
     * @return string
     */
    public function formatReturn($doc = [])
    {
        $json = '{<br>';
        $data = $this->config['return_format'];
        $returns = isset($doc['return']) ? $doc['return'] : [];

        // 预处理：将标准注释格式解析为统一的 {type, name, desc} 结构
        $parsedReturns = [];
        $pureTypes = ['int', 'string', 'array', 'object', 'boolean', 'float', 'json', 'mixed', 'void'];
        foreach ($returns as $val) {
            $val = trim($val);
            if ($val === '') continue;

            if (strpos($val, ':') !== false) {
                // 老格式 key:value 兼容
                $parsedReturns[] = ['raw' => $val, 'format' => 'colon'];
                continue;
            }

            // 标准格式: "type name - desc" 或 "name - desc"
            $valClean = preg_replace('/\s+-\s+/', ' ', $val);
            $parts = preg_split('/\s+/', $valClean, 3);

            if (count($parts) >= 2 && in_array(strtolower($parts[0]), $pureTypes)) {
                $pType = $parts[0];
                $pName = $parts[1] ?? '';
                $pDesc = $parts[2] ?? '';
            } elseif (count($parts) >= 2) {
                $pType = '';
                $pName = $parts[0] ?? '';
                $pDesc = $parts[1] ?? '';
            } else {
                // 只有一个词，检查是否纯类型标记（如 "@return json"），跳过
                if (in_array(strtolower($parts[0]), $pureTypes)) {
                    continue;
                }
                $pType = '';
                $pName = $parts[0] ?? '';
                $pDesc = '';
            }

            $parsedReturns[] = [
                'type'   => $pType,
                'name'   => trim($pName),
                'desc'   => trim($pDesc),
                'format' => 'standard',
            ];
        }

        foreach ($data as $name => $value) {
            if ($name != 'data' || empty($parsedReturns)) {
                $json .= '&nbsp;&nbsp;"' . $name . '":' . $value . ',<br>';
            }
        }

        if (!empty($parsedReturns)) {
            // 将 data.xxx 和 data.list[].xxx 组织成嵌套结构
            $tree = $this->buildReturnTree($parsedReturns);
            $json .= $this->renderReturnTree($tree, '&nbsp;&nbsp;');
        }
        $json .= '}';
        return $json;
    }

    /**
     * 将解析后的 return 注释构建成嵌套树结构
     * 支持 data.list、data.list[].id、data.count 等点号/数组嵌套
     * @param array $items
     * @return array
     */
    private function buildReturnTree($items)
    {
        $tree = [];
        foreach ($items as $item) {
            // 老格式 colon 兼容
            if ($item['format'] === 'colon') {
                $tree[] = $item;
                continue;
            }

            $name = $item['name'];
            $desc = $item['desc'];
            $type = $item['type'];

            // 处理嵌套：data.list[].id → ['data', 'list[]', 'id']
            // 或 data.count → ['data', 'count']
            if (strpos($name, '.') !== false) {
                $segments = explode('.', $name, 10);
                $this->insertIntoTree($tree, $segments, $type, $desc);
            } else {
                $tree[] = ['name' => $name, 'type' => $type, 'desc' => $desc, 'children' => []];
            }
        }
        return $tree;
    }

    /**
     * 递归插入嵌套字段到树中
     * @param array &$tree
     * @param array $segments
     * @param string $type
     * @param string $desc
     */
    private function insertIntoTree(&$tree, $segments, $type, $desc)
    {
        $current = array_shift($segments);
        // 去掉数组标记 [] 用于查找/创建节点
        $cleanName = rtrim($current, '[]');
        $isArray = (substr($current, -2) === '[]');

        // 查找已有节点
        $found = false;
        foreach ($tree as &$node) {
            if (isset($node['name']) && $node['name'] === $cleanName) {
                if ($isArray) {
                    $node['isArray'] = true;
                }
                if (!empty($segments)) {
                    $this->insertIntoTree($node['children'], $segments, $type, $desc);
                }
                $found = true;
                break;
            }
        }
        unset($node);

        if (!$found) {
            $newNode = [
                'name'     => $cleanName,
                'type'     => empty($segments) ? $type : '',
                'desc'     => empty($segments) ? $desc : '',
                'isArray'  => $isArray,
                'children' => [],
            ];
            if (!empty($segments)) {
                $this->insertIntoTree($newNode['children'], $segments, $type, $desc);
            }
            $tree[] = $newNode;
        }
    }

    /**
     * 递归渲染嵌套返回值树为 JSON 展示格式
     * @param array $tree
     * @param string $indent
     * @return string
     */
    private function renderReturnTree($tree, $indent)
    {
        $json = '';
        foreach ($tree as $node) {
            // 老格式 colon 兼容
            if (isset($node['format']) && $node['format'] === 'colon') {
                $raw = $node['raw'];
                list($name, $value) = explode(":", $raw, 2);
                $name = trim($name);
                $value = trim($value);
                if (strpos($value, '@') !== false) {
                    $json .= $this->string2jsonArray([], $name . ':' . $value, $indent);
                } else {
                    $json .= $indent . $this->string2json($name, $value);
                }
                continue;
            }

            $name = $node['name'];
            $desc = $node['desc'] ?? '';
            $isArray = !empty($node['isArray']);
            $children = $node['children'] ?? [];

            if (!empty($children)) {
                // 有子节点，渲染为嵌套对象/数组
                $comment = $desc ? '//' . $desc : '';
                if ($isArray) {
                    $json .= $indent . '"' . $name . '":[{' . $comment . '<br/>';
                } else {
                    $json .= $indent . '"' . $name . '":{' . $comment . '<br/>';
                }
                $json .= $this->renderReturnTree($children, $indent . '&nbsp;&nbsp;');
                if ($isArray) {
                    $json .= $indent . '}],<br/>';
                } else {
                    $json .= $indent . '},<br/>';
                }
            } else {
                // 叶子节点
                $json .= $indent . $this->string2json($name, $desc);
            }
        }
        return $json;
    }
    /**
     * 格式化json字符串-用于展示
     * @param $name
     * @param $val
     * @return string
     */
    private function string2json($name, $val){
        if(strpos($val,'#') != false){
            return '"'.$name.'": ["'.str_replace('#','',$val).'"],<br/>';
        }else {
            return '"'.$name.'":"'.$val.'",<br/>';
        }
    }

    /**
     * 递归转换数组为json字符格式-用于展示
     * @param $doc
     * @param $val
     * @param $space
     * @return string
     */
    private function string2jsonArray($doc, $val, $space){
        // 增加防呆：确保 explode 正常工作
        $parts = explode(":", trim($val), 2);
        $name = isset($parts[0]) ? trim($parts[0]) : '';
        $value = isset($parts[1]) ? trim($parts[1]) : '';
        
        $json = "";
        if(strpos($value, "@!") !== false){
            $json .= $space.'"'.$name.'":{//'.str_replace('@!','',$value).'<br/>';
        }else{
            $json .= $space.'"'.$name.'":[{//'.str_replace('@','',$value).'<br/>';
        }
        $return = isset($doc[$name]) ? $doc[$name] : [];
        if(preg_match_all('/(\w+):(.*?)[\s\n]/s', $return." ", $meatchs)){
            foreach ($meatchs[0] as $key=>$v){
                if(strpos($meatchs[2][$key],'@') !== false){
                    $json .= $this->string2jsonArray($doc,$v,$space.'&nbsp;&nbsp;');
                } else{
                    $json .= $space.'&nbsp;&nbsp;'. $this->string2json(trim($meatchs[1][$key]), $meatchs[2][$key]);
                }
            }
        }
        if(strpos($value, "@!") !== false){
            $json .= $space."}<br/>";
        }else{
            $json .= $space."}]<br/>";
        }
        return $json;
    }
    public function cleanCommentArray($items, $isHeader = false)
    {
        if (!is_array($items)) return [];
        $cleaned = [];
        
        foreach ($items as $item) {
            // 如果原本就是老包认识的带name的结构，直接保留
            if (is_array($item) && isset($item['name'])) {
                $cleaned[] = $item;
                continue;
            }
            
            // 如果是标准字符串注释，如 "string keywords - 查找内容"
            if (is_string($item)) {
                $itemStr = trim($item);
                $itemStr = preg_replace('/\s+-\s+/', ' ', $itemStr); // 去掉减号
                $parts = preg_split('/\s+/', $itemStr, 3);
                
                $pName = '-';
                $pType = '-';
                $pDesc = '-';
                
                if (count($parts) >= 2) {
                    $types = ['int', 'string', 'array', 'object', 'boolean', 'float', 'mixed', 'json'];
                    if (in_array(strtolower($parts[0]), $types)) {
                        $pType = $parts[0];
                        $pName = $parts[1] ?? '-';
                        $pDesc = $parts[2] ?? '-';
                    } else {
                        $pName = $parts[0];
                        $pDesc = $parts[1] ?? '-';
                    }
                } else {
                    $pName = $parts[0] ?? '-';
                }
                
                $pName = ltrim($pName, '$');
                
                if ($isHeader) {
                    $cleaned[] = [
                        'name'    => $pName,
                        'require' => '0',
                        'default' => '-',
                        'desc'    => $pDesc
                    ];
                } else {
                    $cleaned[] = [
                        'name'    => $pName,
                        'type'    => $pType,
                        'require' => (strpos($pDesc, '必填') !== false || strpos($pDesc, 'require') !== false) ? '1' : '0',
                        'default' => '-',
                        'other'   => '-',
                        'desc'    => $pDesc
                    ];
                }
            }
        }
        return $cleaned;
    }
}