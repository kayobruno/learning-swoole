<?php

declare(strict_types=1);

namespace App\WebSocket;

use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

class Server
{
    private SwooleWebSocketServer $server;

    public function __construct(string $host = "0.0.0.0", int $port = 9501)
    {
        $this->server = new SwooleWebSocketServer($host, $port);

        $this->server->on("open", [$this, "onOpen"]);
        $this->server->on("message", [$this, "onMessage"]);
        $this->server->on("close", [$this, "onClose"]);
    }

    public function start(): void
    {
        echo "WebSocket server started on ws://127.0.0.1:9501\n";
        $this->server->start();
    }

    public function onOpen(SwooleWebSocketServer $server, Request $request): void
    {
        echo "Connection opened: {$request->fd}\n";
    }

    public function onMessage(SwooleWebSocketServer $server, Frame $frame): void
    {
        echo "Received message: {$frame->data}\n";
        $server->push($frame->fd, "Server received: " . $frame->data);
    }

    public function onClose(SwooleWebSocketServer $server, int $fd): void
    {
        echo "Connection closed: {$fd}\n";
    }
}
