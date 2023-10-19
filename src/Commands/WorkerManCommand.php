<?php

namespace atshike\plwh\Commands;


use atshike\plwh\Events\WorkermanEvents;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class WorkerManCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:workman {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        global $argv;
        $action = $this->argument('action');
        if (! in_array($action, ['status', 'start', 'stop', 'restart', 'reload', 'connections'])) {
            exit("action invalid! \n");
        }
        $argv[0] = 'wk';
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';

        $this->start();
    }

    private function start(): void
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        $this->log();

        Worker::runAll();
    }

    private function startGateWay(): void
    {
        $worker_man = config('services.worker_man');
        //gateway进程
        $gateway = new Gateway("websocket://0.0.0.0:{$worker_man['port']}");
        //gateway名称 status方便查看
        $gateway->name = 'Gateway';
        //gateway进程
        $gateway->count = 2;
        //本机ip
        $gateway->lanIp = '127.0.0.1';
        //内部通讯起始端口，如果$gateway->count = 4 起始端口为2300
        //则一般会使用 2300，2301 2个端口作为内部通讯端口
        $gateway->startPort = $worker_man['start_port'];
        //心跳间隔
        $gateway->pingInterval = 30;
        //客户端连续$pingNotResponseLimit次$pingInterval时间内不发送任何数据则断开链接，并触发onClose。
        //我们这里使用的是服务端主动发送心跳所以设置为0
        $gateway->pingNotResponseLimit = 0;
        //心跳数据
        $gateway->pingData = '{"type":"pong"}';
        //服务注册地址
        $gateway->registerAddress = $worker_man['register_address'];
    }

    private function startBusinessWorker(): void
    {
        $register_address = config('services.worker_man.register_address');
        $worker = new BusinessWorker();
        //work名称
        $worker->name = 'BusinessWorker';
        //businessWork进程数
        $worker->count = 2;
        //服务注册地址
        $worker->registerAddress = $register_address;
        //设置AppWorkermanEvents类来处理业务
        $worker->eventHandler = WorkermanEvents::class;
    }

    private function startRegister(): void
    {
        $register_service = config('services.worker_man.register_service');
        new Register($register_service);
    }

    private function log(): void
    {
        if (! config('services.worker_man.log')) {
            return;
        }

        $workerPath = storage_path('workerman/');
        if (! is_dir($workerPath)) {
            mkdir($workerPath, 0755, true);
        }
        $logPath = $workerPath.date('Ym').'/';
        if (! is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }
        Worker::$logFile = $logPath.date('d').'.log';
    }
}
