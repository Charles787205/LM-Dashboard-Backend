<?php

namespace App\Enums;

enum PositionEnum: string {
  case MANAGER = 'manager';
  case ADMIN = 'admin';
  case HUB_LEAD = 'hub_lead';
  case BACKROOM = 'backroom';

  public static function values(): array
  {
      return array_map(fn($position) => $position->value, self::cases());
  }
}