<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    /**
     * Get all available roles as an array
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all roles as an array for select inputs
     */
    public static function getSelectOptions(): array
    {
        return collect(self::cases())->mapWithKeys(function ($role) {
            return [$role->value => ucfirst($role->value)];
        })->all();
    }
}
