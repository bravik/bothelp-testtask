<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

// Config
return [
    'message_bus' => new \App\RabbitQueue('bothelp_rabbitmq', 5672, 'guest', 'guest'),
    'output_file' => __DIR__ . '/var/output.txt',
];