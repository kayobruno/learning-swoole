<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Swoole\Http\{Server, Request, Response};

require 'vendor/autoload.php';

Co::set(['hook_flags'=> SWOOLE_HOOK_ALL]);

$server = new Server('0.0.0.0', 8080);

$server->on('Request', function(Request $request, Response $response)
{
	if ('/send-message' === $request->server['request_uri']) {
		$body = json_decode($request->getContent());
		$message = $body->message ?? '';


		$log = new Logger('app');

		$channel = new chan(2);

		go(function () use ($log, $message, $channel) {
			$log->pushHandler(new StreamHandler('requests.log', Logger::INFO));
			$log->info($message);
			$channel->push(['foo' => true]);
		});

		go(function () use ($log, $channel) {
			Co::sleep(10);
			for ($i=0; $i<10000; $i++) {
				$log->info($i);
			}

			$channel->push(['bar' => true]);
		});

		go(function () use (&$response, $channel) {
			$foo = $channel->pop();
			$bar = $channel->pop();

			$response->header("Content-Type", "application/json; charset=utf-8");
			$response->end(json_encode(['message' => 'Mensagem enviada com sucesso']));
		});
	}
});

$server->start();