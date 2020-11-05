<?php

/**
 * @author Roman Naumenko <naumenko_subscr@mail.ru>
 */

declare(strict_types=1);

use App\Message;
use App\MessageBusInteface;

$config = require "bootstrap.php";

$messageHandler = static function (Message $message) use ($config) {
    sleep(1);
    $log = "Account: {$message->accountId}. Message: {$message->messageNumber}" . PHP_EOL;
    echo $log;
    file_put_contents($config['output_file'], $log, FILE_APPEND | LOCK_EX);
};


/** @var MessageBusInteface $messageBus */
$messageBus = $config['message_bus'];

$messageBus->connect();

try {
    echo "Listening..." . PHP_EOL;
    $messageBus->listen($messageHandler);
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}

$messageBus->disconnect();

