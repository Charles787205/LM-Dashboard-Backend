<?php

namespace App\Enums;

enum AccessRoleEnum: string
{
    case VIEWER = 'viewer';
    case EDITOR = 'editor';
    case RESTRICTED = 'restricted';
    public static function values(): array
    {
        return array_map(fn($role) => $role->value, self::cases());
    }
}
