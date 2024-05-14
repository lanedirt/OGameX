<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\MessageType;
use OGame\GameMessages\Messages\ReturnOfFleet;
use OGame\GameMessages\Messages\ReturnOfFleetNoGoods;

class MessageTypeRegistry
{
    private static array $messageTypes = [];

    public static function registerMessageType(MessageType $messageType): void
    {
        self::$messageTypes[$messageType->getId()] = $messageType;
    }

    public static function getMessageType(int $id): ?MessageType
    {
        return self::$messageTypes[$id] ?? null;
    }

    public static function getAllMessageTypes(): array
    {
        return self::$messageTypes;
    }
}

// Register message types
MessageTypeRegistry::registerMessageType(new ReturnOfFleet());
MessageTypeRegistry::registerMessageType(new ReturnOfFleetNoGoods());
