<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_web_socket(): void
    {
        $port = config('services.worker_man.port');
        if (false === ($client = stream_socket_client("tcp://127.0.0.1:{$port}", $error_code, $error_message, 3))) {
            echo '连接失败'.PHP_EOL;
            var_dump($error_code, $error_message);
            exit(1);
        }
        echo '连接成功'.PHP_EOL;
        fwrite($client, json_encode(['type' => 'login', 'name' => 'xx', 'room_id' => 1])."\n");
        fclose($client);
    }
}
