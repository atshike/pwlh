# workerman 聊天室 Websocket + HTLM5+PHP多进程socket

### 安装
```
composer require atshike/pwlh
```
### 生成command
```
1.
php artisan make:command WorkerManCommand

2.
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
### 配置文件
- config/service.php
```
'worker_man' => [
    'port' => env('WORKER_MAN_PORT', 2346),
    'start_port' => env('WORKER_MAN_START_PORT', 2300),
    'log' => env('WORKER_MAN_LOG', 1),
    'register_service' => env('REGISTER_SERVICE', 'text://0.0.0.0:1236'),
    'register_address' => env('REGISTER_ADDRESS', '127.0.0.1:1236'),
],

```
### 进程守候
- 配置 Supervisor
```
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
