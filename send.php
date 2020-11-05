<?php
declare(strict_types=1);

use App\Message;
use App\MessageBusInteface;

$config = require "bootstrap.php";

/** @var MessageBusInteface $messageBus */
$messageBus = $config['message_bus'];

$maxUsers = isset($argv[1]) ? (int) $argv[1] : 1000;
$maxMessages = isset($argv[2]) ? (int) $argv[2] : 10000;

$users = [];

$messageBus->connect();


// Sends single message to random user until limit of messages reached
try {
    for ($m = 1; $m <=$maxMessages; $m++) {
        $userId = random_int(1, $maxUsers);
        $messageNumber = $users[$userId] ?? 0;

        $message = new Message($userId, ++$messageNumber);
        $messageBus->send($message); // TODO Batch? Seems fine for testing purposes

        $users[$userId] = $messageNumber;
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}

$messageBus->disconnect();

echo "Messages sent" . PHP_EOL;