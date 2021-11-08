<?php

$server = new Swoole\Server("127.0.0.1", 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);

$server->on('Connect', function ($server, $fd)
{
    echo "Client: Connect\n";
});

$server->on('Receive', function ($server, $fd, $reactor_id, $data)
{
	$responseEchoTest = ' KISO0234000700810822200000200000004000000000000001103091310239410110300301';
	$responseSIgnOn   = ' KISO0234000700810822200000200000004000000000000001103091309151311110300001';

	$messageType = substr($data, strlen($data) - 3);

	$response = $data;
	if ($messageType === '001') {
		$response = $responseSIgnOn;
	} elseif ($messageType === '301') {
		$response = $responseEchoTest;
	}

    $server->send($fd, "{$response}");
});

$server->on('Close', function ($server, $fd)
{
    echo "Client: Close\n";
});

$server->start();