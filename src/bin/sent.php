<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bunny\Client;

$client = new Client([
    'host' => 'rabbitmq:5672',
    'vhost' => '/',
    'user' => 'guest',
    'password' => 'guest',
]);

$queneName = 'crude_queue5';

try {
    $client->connect();
    $channel = $client->channel();

    $message = json_encode([
        'script' => 'test.php',
    ]);

    $channel->queueDeclare($queneName, durable: true);
    $channel->publish($message, routingKey: $queneName);

    echo " [x] Sent message: $message\n";
} catch (\Exception $e) {
    echo $e->getMessage();
}

