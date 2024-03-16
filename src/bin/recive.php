<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;

$connection = [
    'host'      => 'rabbitmq:5672',
    'vhost'     => '/',
    'user'      => 'guest',
    'password'  => 'guest',
];

$queneName = 'crude_queue51';

$bunny = new Client($connection);

try {
    $bunny->connect();

    $channel = $bunny->channel();
    $channel->queueDeclare($queneName, durable: true);

    $channel->qos(prefetchCount: 1);

    echo " [*] Waiting for messages. To exit press Ctrl+C", "\n";

    $channel->run(
        function (Message $message, Channel $channel) {

            $success = ! empty($message->content);

            echo " [x] Received message: ", $message->content, PHP_EOL;
            $script = json_decode($message->content, true)['script'];

            if ($success) {
                exec("php /var/www/bin/$script", $output, $returnCode);
                echo " [x] Script " . ($returnCode !== 0 ? "failed" : "executed successfully") . ": $script", PHP_EOL;

                $channel->ack($message);
                return;
            }

            dd($channel->nack($message));
        },
        $queneName
    );
} catch (\Exception $e) {
    echo $e->getMessage();
}
