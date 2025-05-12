<?php

declare(strict_types=1);

namespace App\WebSocket;

use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\Timer;

class Server
{
    private SwooleWebSocketServer $server;
    private array $clients = [];
    private array $units = [];

    public function __construct(string $host = "0.0.0.0", int $port = 9501)
    {
        $this->server = new SwooleWebSocketServer($host, $port);

        $this->server->on("open", [$this, "onOpen"]);
        $this->server->on("message", [$this, "onMessage"]);
        $this->server->on("close", [$this, "onClose"]);

        $this->initMockData();
        $this->startAutoUpdate();
    }

    private function initMockData(): void
    {
        $this->units = [
            [
                "name" => "Unidade A",
                "ip" => "10.0.0.1",
                "link" => "http://10.0.0.1",
                "version" => "2.0.1",
                "last_update" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Unidade B",
                "ip" => "10.0.0.2",
                "link" => "http://10.0.0.2",
                "version" => "1.8.0",
                "last_update" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Unidade C",
                "ip" => "10.0.0.3",
                "link" => "http://10.0.0.3",
                "version" => "1.8.0",
                "last_update" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Unidade D",
                "ip" => "10.0.0.4",
                "link" => "http://10.0.0.4",
                "version" => "1.8.0",
                "last_update" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Unidade F",
                "ip" => "10.0.0.5",
                "link" => "http://10.0.0.5",
                "version" => "1.8.0",
                "last_update" => date('Y-m-d H:i:s'),
            ],
        ];
    }

    private function startAutoUpdate(): void
    {
        Timer::tick(5000, function () {
            $index = array_rand($this->units);
            $this->units[$index]['last_update'] = date('Y-m-d H:i:s');
            $this->units[$index]['version'] = (int)$this->units[$index]['version'] . "." . rand(1, 9);  

            foreach ($this->clients as $fd) {
                if ($this->server->isEstablished($fd)) {
                    $this->server->push($fd, json_encode($this->units));
                }
            }
        });
    }

    public function start(): void
    {
        echo "WebSocket server started on ws://127.0.0.1:9501\n";
        $this->server->start();
    }

    public function onOpen(SwooleWebSocketServer $server, Request $request): void
    {
        $uri = $request->server['request_uri'] ?? '/';
        if ($uri !== '/units') {
            echo "Conexão recusada de {$request->fd} em URI inválida: {$uri}\n";
            $server->disconnect($request->fd, 1003, "Invalid WebSocket endpoint");
            return;
        }

        echo "Connection opened: {$request->fd}\n";
        $this->clients[$request->fd] = $request->fd;
    }

    public function onMessage(SwooleWebSocketServer $server, Frame $frame): void
    {
        echo "Received message from {$frame->fd}: {$frame->data}\n";

        if (trim($frame->data) === 'get_units') {
            $server->push($frame->fd, json_encode($this->units));
        } else {
            $server->push($frame->fd, "Comando inválido. Envie 'get_units' para obter os dados.");
        }
    }

    public function onClose(SwooleWebSocketServer $server, int $fd): void
    {
        echo "Connection closed: {$fd}\n";
        unset($this->clients[$fd]);
    }
}
