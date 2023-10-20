# pwlh
workerman PHP聊天室 Websocket + HTLM5+PHP多进程socket

```angular2html
composer require atshike/pwlh

php artisan make:command WorkerManCommand

global $argv;
$action = $this->argument('action');
if (! in_array($action, ['status', 'start', 'stop', 'restart', 'reload', 'connections'])) {
exit("action invalid! \n");
}
$argv[0] = 'wk';
$argv[1] = $action;
$argv[2] = $this->option('d') ? '-d' : '';

WorkerManService::start();

```

```angular2html
安装workerman
composer require workerman/gateway-worker
php artisan app:workman start --d
```


```angular2html
代码格式
composer require laravel/pint --dev
./vendor/bin/pint
```

```angular2html
进程守候  
配置 Supervisor
cd /etc/supervisor/conf.d
vim laravel-worker.conf

[program:laravel-worker]
command=/usr/bin/php8.1 /home/crawler/artisan app:workman start
numprocs=1
autostart=true
autorestart=true
user=root

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```
