<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$db = new PDO('mysql:host=mysql;dbname=default', 'root', 'root');

$channel->queue_declare('task_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) use ($db) {
    $message = $msg->getBody();

    try {
        // Подготовить SQL-запрос для вставки в таблицу tasks
        //INSERT INTO `tasks` (`title`, `message`, `status_id`) VALUES ('test', '{script: test.php}', 1);
        $query = "
            INSERT INTO `tasks`
            (`title`, `message`, `status_id`)
            VALUES (:title, :message, :status_id)
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':title' => 'crude',
            ':message' => $message,
            ':status_id' => 1, // Устанавливаем первоначальный статус как 'new'
        ]);

        // Подтвердить получение сообщения
        $msg->ack();

        echo " [x] Received and saved to database: $message\n";
    } catch (Exception $e) {
        echo " [x] Error saving message to database: " . $e->getMessage() . "\n";
        // Отклонить получение сообщения при ошибке записи в БД, чтобы RabbitMQ переотправил его
        $msg->nack();
        // Логировать ошибку
        error_log($e->getMessage());
    }
};

$channel->basic_qos(null, 1, false);
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
