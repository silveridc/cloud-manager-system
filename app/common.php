<?php
use think\facade\Config;

/**
 * curl请求
 *
 * @param string url - url地址 require
 * @param array data [] 传递的参数
 * @param string timeout 30 超时时间
 * @param string request POST 请求类型
 * @param array header [] 头部参数
 * @param  bool curlFile false 是否curl上传文件
 * @return int http_code - http状态码
 * @return string error - 错误信息
 * @return string content - 内容
 */
function curl($url, $data = [], $timeout = 30, $method = 'GET', $header = [], $curlFile = false)
{
    //初始化
    $ch = curl_init();
    $method = strtoupper($method);
    if ($method === 'GET' && !empty($data)) {
        $queryString = is_array($data) ? http_build_query($data) : $data;
        $url .= (str_contains($url, '?') ? '&' : '?') . $queryString;
    }
    //设置
    $options = [
        CURLOPT_URL            => $url,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER         => false,
        CURLOPT_USERAGENT      => config('app.curl.user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36'),
        CURLOPT_REFERER        => request()->host(),
        // SSL 验证
        CURLOPT_SSL_VERIFYPEER => config('app.curl.ssl_verify', false),
        CURLOPT_SSL_VERIFYHOST => config('app.curl.ssl_verify_host', false),
    ];
    if ($method === 'POST') {
        $options[CURLOPT_POST] = true;
    } elseif ($method !== 'GET') {
        $options[CURLOPT_CUSTOMREQUEST] = $method;
    }
    if ($method !== 'GET' && !empty($data)) {
        if (is_array($data) && !$curlFile) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        } else {
            $options[CURLOPT_POSTFIELDS] = $data;
        }
    }
    if (!empty($header)) {
        $options[CURLOPT_HTTPHEADER] = $header;
    }
    curl_setopt_array($ch, $options);
    $content   = curl_exec($ch);
    $error     = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = ['http_code' => $http_code,'error' => $error,'content' => $content];

    return $result;
}

/**
 * 金额格式化
 */
function amount_format(float|string $amount): string
{
    return number_format((float)$amount, 2, '.', '');
}

/**
 * @title 密码加密
 * @desc 密码加密
 * @author zhaoyj
 * @version v1
 * @param string $password - 密码
 * @param string $salt - 盐
 * @config array password - 密码配置
 * @config string .salt - 密码加盐配置项
 * @config bool .is_zjmf - 魔方迁移
 * @return string 加密后密码
 */
function cmf_password(string $password,string $salt = '')
{
    if (!$salt) {
        $password_salt = config('app.password.salt');
    } else {
        $password_salt = $salt;
    }
    if (config('app.password.is_zjmf', false)) {
        $result = '###' . md5(md5($password_salt . $password));
    } else {
        $result = '###'.hash('sha256', $password_salt.$password);
    }
    return $result;
}