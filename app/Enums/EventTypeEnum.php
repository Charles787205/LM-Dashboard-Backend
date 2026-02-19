<?php

namespace App\Enums;

enum EventTypeEnum: string
{
    //create update delete
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';

    public static function values(): array
    {
        return array_map(fn($event) => $event->value, self::cases());
    }
}
