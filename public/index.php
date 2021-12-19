<?php

use App\Messages\NetworkMessage;
use Swoole\Http\{Server, Request, Response};
use Swoole\Coroutine\Client;
use Swoole\Timer;

require 'vendor/autoload.php';

Co::set(['hook_flags'=> SWOOLE_HOOK_ALL]);

$host = '127.0.0.1';
$port = 9501;
$client = new Client(SWOOLE_SOCK_TCP);
$channel = new chan(10);

Co\run(function () use ($client, $host, $port, $channel) {
    if (!$client->connect($host, $port)) {
        throw new \Exception("Connection failed with error: {$client->errCode}\n");
    }
});

$server = new Server('0.0.0.0', 8080);

$server->on('Request', function(Request $request, Response $response) use ($server, $client, $channel)
{
	if ('/messages' === $request->server['request_uri']) {
		$body = json_decode($request->getContent());
		$message = $body->message ?? '';
        $paymentId = $body->payment_id ?? null;

		go(function () use ($client, $channel, $message, $paymentId) {
            $client->send($message);
            $data = $client->recv();
            $channel->push(['message' => $data, 'payment_id' => $paymentId]);
		});

		go(function () use (&$response, $channel, $paymentId) {
            $data = ['message' => 'Não foi possível enviar a mensagem'];
			$response = $channel->pop();
			if (!empty($response)) {
				$data = ['iso_response' => $response['message'], 'payment_id' => $data['payment_id']];
			}

			$response->header("Content-Type", "application/json; charset=utf-8");
			$response->end(json_encode($data));
		});
	}
});

$server->on('Start', function (Server $server) use ($client) {
    $signOnMessage = NetworkMessage::createSignOff(new DateTime());
    $client->send($signOnMessage);

	Timer::tick(
		1000 * 30,
		function () use ($client) {
            $echoTestMessage = NetworkMessage::createEchoTest(new DateTime());
            $client->send($echoTestMessage);
            echo '[Message] = "' . $echoTestMessage . '"' . PHP_EOL;
		}
	);
});

$server->start();
