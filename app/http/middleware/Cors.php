<?php /** @noinspection PhpRedundantOptionalArgumentInspection */

namespace app\http\middleware;

use think\Request;
use think\Response;

/**
 * 跨域中间件
 * @desc 跨域中间件
 * @uses \app\http\middleware\Cors
 */
class Cors
{
    /**
     * 允许跨域的域名白名单
     */
    private array $allowedOrigins = [];

    public function __construct()
    {
        // 从配置读取允许的域名列表，默认为空（不允许任何跨域）
        $this->allowedOrigins = config('app.cors.allowed_origins', []);
    }

    /**
     * @title 跨域请求处理
     * @desc 跨域请求处理
     * @author zhaoyj
     * @version v1
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $origin = $request->header('Origin', '');

        // 验证 Origin 是否在白名单中
        $allowOrigin = '';
        if ($origin && in_array($origin, $this->allowedOrigins)) {
            $allowOrigin = $origin;
        }

        if ($request->isOptions()) {
            $response = response('')->code(204);
        } else {
            $response = $next($request);
        }

        if ($allowOrigin) {
            $response->header([
                'Access-Control-Allow-Origin' => $allowOrigin,
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
                'Access-Control-Allow-Headers' => 'Authorization, Content-Type, Accept, X-Requested-With',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age' => '1800',
            ]);
        }

        return $response;
    }
}