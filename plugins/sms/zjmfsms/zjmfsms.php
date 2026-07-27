<?php
namespace sms\zjmfsms;
use app\common\contract\PluginInterface;
class zjmfsms implements PluginInterface
{
    public function __construct()
    {
        $this->baseurl = 'http://api1.idcsmart.com/smsapi.php';
    }
    public function info(): array
    {
        return [
            'name'        => 'zjmfsms',
            'title'       => '魔方短信',
            'description' => '魔方短信',
            'author'      => 'zhaoyj',
            'version'     => '1.0.0',
        ];
    }
    public function install()
    {
        return true;
    }
    public function uninstall()
    {
        return true;
    }
    public function CreateSmsTemplate()
    {
        $url = $this->baseurl.'?action=template';
        $data = [
            'title' => config('zjmfsms.template')
        ];
        $header = [
            'api' => config('zjmfsms.api'),
            'key' => config('zjmfsms.key'),
        ];
        curl($url, $data, 30, 'POST', $header);
    }
}