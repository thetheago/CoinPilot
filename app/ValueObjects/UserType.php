<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

class UserType
{
    public const COMMON = 'comum';
    public const LOJISTA = 'lojista';

    private const ALLOWED_TYPES = [self::COMMON, self::LOJISTA];
    private string $value;

    public function __construct(string $type)
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new InvalidArgumentException(
                sprintf(
                    'User type inválido, os tipos válidos são: %s',
                    implode(', ', self::ALLOWED_TYPES)
                )
            );
        }

        $this->value = $type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isCommon(): bool
    {
        return $this->value === self::COMMON;
    }

    public function isLojista(): bool
    {
        return $this->value === self::LOJISTA;
    }
}
