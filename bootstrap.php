<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\WebSocket\Server;

$server = new Server();
$server->start();
