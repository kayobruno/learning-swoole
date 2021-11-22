<?php

use Swoole\Coroutine\Channel;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Swoole\Http\{Server, Request, Response};
use Swoole\Coroutine\Client;
use Swoole\Constant;
use Swoole\Server\Task;
use Swoole\Timer;

require 'vendor/autoload.php';

Co::set(['hook_flags'=> SWOOLE_HOOK_ALL]);

$log = new Logger('app');
$log->pushHandler(new StreamHandler('requests.log', Logger::INFO));

$server = new Server('0.0.0.0', 8080);
$server->set([
//	'worker_num'        	=> 2,
//	'task_worker_num'   	=> swoole_cpu_num(),
	'message_queue_key'     => 'bbva_queue',
//	'task_enable_coroutine' => true,
]);

// Recebe os requests HTTP
$server->on('Request', function(Request $request, Response $response) use ($log, $server)
{
	if ('/send-message' === $request->server['request_uri']) {
		$body = json_decode($request->getContent());
		$message = $body->message ?? '';

		$channel = new chan(1);
		go(function () use ($channel, $message) {
			$host = '127.0.0.1';
			$port = 9501;

			$client = new Client(SWOOLE_SOCK_TCP);
			if (!$client->connect($host, $port)) {
				echo "Connection failed with error: {$client->errCode}\n";
			}

			$client->send($message);
			$data = $client->recv();

			$client->close();
			$channel->push($data);
		});

		$data = ['message' => 'Error'];
		go(function () use (&$response, $channel, $data) {
			$iso_response = $channel->pop();
			if (!empty($iso_response)) {
				$data = ['message' => 'Mensagem enviada com sucesso', 'iso_response' => $iso_response];
			}

			$response->header("Content-Type", "application/json; charset=utf-8");
			$response->end(json_encode($data));
		});
	}
});

// Evento acionado no start do servidor
$server->on('Start', function (Server $server) {
	echo '[HTTP1-ADVANCED]: # of CPU units: ', swoole_cpu_num(), "\n";

	// Here we start the first cron job that runs every 60 seconds.
	Timer::tick(
		1000 * 30,
		function () {
			// Enviar mensagem de rede
			echo '[HTTP1-ADVANCED]: This message is printed out every 30 seconds. (', date('H:i:s'), ")\n";
		}
	);
});

$server->start();
