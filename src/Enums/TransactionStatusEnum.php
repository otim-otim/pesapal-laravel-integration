<?php

namespace OtimOtim\PesapalIntegrationPackage\Enums;

enum TransactionStatusEnum
{
    case PENDING;
    case COMPLETED;
    case FAILED;
    case CANCELLED;
    case REVERSED;
    case INVALID;

    public static function toArray(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }
}