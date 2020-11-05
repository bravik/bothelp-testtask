<?php

$config = require 'bootstrap.php';

$lines = explode(PHP_EOL, file_get_contents($config['output_file']));
if (count($lines) === 0) {
    echo "Output is empty";
}

$accountToMessages = [];
foreach ($lines as $line) {
    if (empty(trim($line))) {
        continue;
    }
    preg_match_all('/Account: (\d*). Message: (\d*)/', $line, $matches, );
    $account = isset($matches[1]) ? (int) $matches[1] : null;
    $messageNumber = isset($matches[2]) ? (int) $matches[2] : null;
    if (!$account || !$messageNumber) {
        echo "Wrong output" . PHP_EOL;
        echo "Failed on line: $line" . PHP_EOL;
        die;
    }
    if (!isset($accountToMessages[$account])) {
        $accountToMessages[$account] = $messageNumber;
        continue;
    }

    if ($accountToMessages[$account] > $messageNumber) {
        echo "Test failed: messages are not in correct order" . PHP_EOL;
        echo "Failed on line: $line" . PHP_EOL;
        die;
    }
}

echo "Test passed. Everything is in order." . PHP_EOL;
