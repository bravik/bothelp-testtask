<?php

/**
 * @author Roman Naumenko <naumenko_subscr@mail.ru>
 */

declare(strict_types=1);

namespace App;

use JsonSerializable;
use RuntimeException;

class Message implements JsonSerializable
{
    public int $accountId;
    public int $messageNumber;

    public function __construct(int $accountId, int $messageNumber)
    {
        $this->accountId = $accountId;
        $this->messageNumber = $messageNumber;
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['accountId'], $data['messageNumber'])) {
            throw new RuntimeException('Failed to create Message from array');
        }

        return new self($data['accountId'], $data['messageNumber']);
    }

    public function jsonSerialize(): array
    {
        return [
            'accountId' => $this->accountId,
            'messageNumber' => $this->messageNumber,
        ];
    }
}
