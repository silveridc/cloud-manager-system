![Language](https://img.shields.io/badge/language-php-green?style=for-the-badge)
![Framework](https://img.shields.io/badge/framework-thinkphp-green?style=for-the-badge)
## 财务系统v1.0后端

### 本地部署与启动

使用方法:
前置条件：php(8.0+ 需要fpm) composer mysql(pgsql似乎也可以，但是没有测试) nginx/apache/ols(只要能运行php就能启动)

下载本项目源码 或使用```git clone https://github.com/silveridc/cloud-manager-system.git ./``` 
```bash
  composer install
```

### Nginx(Tengine,OpenResty ...)
请复制以下内容至你的server block中
```nginx
location / {
  if (!-e $request_filename) {
    rewrite ^(.*)$ /index.php?s=$1 last;
  break;
  }
}
```

### Apache系(OLS/Kangle ...)
- public下已有htaccess

## 使用宝塔面板
- 请在网站->添加站点新建 PHP版本8.0+
- 创建完成后 在网站的配置->网站目录->运行目录下 把/选择为/public
- 若使用nginx 请 在新建的这个站点的配置->伪静态内 填入上方nginx的url重写配置 或者选择伪静态为thinkphp

## 内嵌server
- 在目录下 执行
- php think run命令或php -S localhost:8080 即可启动
- 若默认端口提示被占用 可执行php think run -p 你要指定的端口

## 致谢:
[weyeah](https://github.com/weyeahh)

[makotowu](https://github.com/makotowu)

欢迎提交 Issue 或 pr 来帮助改进项目。
