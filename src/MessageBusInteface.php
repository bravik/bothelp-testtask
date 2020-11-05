<?php

/**
 * @author Roman Naumenko <naumenko_subscr@mail.ru>
 */

declare(strict_types=1);

namespace App;

interface MessageBusInteface
{
    public function connect(): void;
    public function send(Message $message): void;
    public function listen(callable $messageProcessor): void;
    public function disconnect(): void;
}
