<?php

namespace App\Enums;

enum ProductStatusEnum: String
{

    case Draft = 'draft';
    case Published = 'published';
    case OutOfStock = 'out of stock';

    public static function labels()
    {
        return [
            self::Draft->value => __('Draft'),
            self::OutOfStock->value  => __('Out Of Stock'),
            self::Published->value  => __('Published'),
        ];
    }

    public static function colors(): array
    {

        return [
            'gray' => self::Draft->value,
            'red' => self::OutOfStock->value,
            'success' => self::Published->value,
        ];
    }
}
