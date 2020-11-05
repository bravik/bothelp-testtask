<?php

/**
 * @author Roman Naumenko <naumenko_subscr@mail.ru>
 */

declare(strict_types=1);

namespace App;

use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use RuntimeException;

class RabbitQueue implements MessageBusInteface
{
    private AbstractConnection $connection;
    private AbstractChannel $channel;
    private const EXCHANGE_USER_MESSAGES = 'user-messages';

    private string $host;
    private int $port;
    private string $login;
    private string $password;

    public function __construct(string $host, int $port, string $login, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->login = $login;
        $this->password = $password;
    }

    public function connect(): void
    {
        if (!$this->isConnected()) {
            $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->login, $this->password);
            $this->channel = $this->connection->channel();

            $this->channel->basic_qos(0, 1, false);
            $this->channel->exchange_declare(
                self::EXCHANGE_USER_MESSAGES,
                'x-consistent-hash',
                false,
                false,
                false
            );
        }
    }

    public function disconnect(): void
    {
        if ($this->isConnected()) {
            $this->connection->close();
        }
    }

    public function send(Message $message): void
    {
        if (!$this->isConnected()) {
            throw new RuntimeException("Message bus is disconnected. Use connect() method first");
        }

        $msg = new AMQPMessage(json_encode($message));
        $this->channel->basic_publish(
            $msg,
            self::EXCHANGE_USER_MESSAGES,
            (string)$message->accountId
        );

    }

    public function listen(callable $messageProcessor): void
    {
        if (!$this->isConnected()) {
            throw new RuntimeException("Message bus is disconnected. Use connect() method first");
        }

        $queueKey = uniqid('', true);
        $this->channel->queue_declare($queueKey, false, false, false, false);
        $this->channel->queue_bind($queueKey, self::EXCHANGE_USER_MESSAGES, "1");


        $callback = function ($msg) use ($messageProcessor) {
            $messageProcessor(
                Message::fromArray(
                    json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR)
                )
            );
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $this->channel->basic_consume(
            $queueKey,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    private function isConnected(): bool
    {
        return isset($this->connection) && $this->connection->isConnected();
    }
}
