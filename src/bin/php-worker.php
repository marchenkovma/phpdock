<?php

$db = new PDO('mysql:host=mysql;dbname=default', 'root', 'root');

// Функция для обработки задачи
function processTask(PDO $db, int $taskId): void
{
    // Получить задачу из таблицы tasks
    $query = "
        SELECT *
        FROM `tasks`
        WHERE `id` = :id
    ";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        echo " [x] Task with ID $taskId not found\n";
        return;
    }

    echo " [x] Received task $taskId\n";
    // Изменить статус на 'pending'
    $query = "
        UPDATE `tasks`
        SET `status_id` = 2
        WHERE `id` = :id
    ";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $taskId]);

    // Выполнить действие с сообщением
    $message = $task['message'];
    // ... ваш код для выполнения действия ...

    // Записать результат действия в файл
    try {
        $file = fopen('log.txt', 'a');
        fwrite($file, date('Y-m-d H:i:s') . " - $message\n");
        fclose($file);
        sleep(10);
        $status_id = 3;
    } catch (Exception $e) {
        echo " [x] Error writing to file: " . $e->getMessage() . "\n";
        // Изменить статус на 'failed'
        $status_id = 4;
    }

    // Обновить время и статус задачи
    $query = "
        UPDATE `tasks`
        SET `message` = :message,
            `status_id` = :status_id
        WHERE `id` = :id";
    //$stmt = $db->prepare("UPDATE tasks SET message = :message, updated_at = CURRENT_TIMESTAMP, status = :status WHERE id = :id");

    $stmt = $db->prepare($query);

    $stmt->execute([
        ':message' => $message,
        ':status_id' => $status_id,
        ':id' => $taskId,
    ]);

    echo " [x] Task $taskId processed with status $status_id\n";
}

// Цикл обработки задач
while (true) {
    // Получить ID задачи с 'new' статусом
    $query = "
        SELECT `id`
        FROM `tasks`
        WHERE `status_id` = 1
        ORDER BY id
        ASC LIMIT 1
    ";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $taskId = $stmt->fetchColumn();

    if (!$taskId) {
        echo " [x] No tasks to process\n";
        sleep(5); // Задержка 5 секунд, если нет задач
        continue;
    }

    // Обработать задачу
    processTask($db, $taskId);
}
