<?php

namespace OtimOtim\PesapalIntegrationPackage\Enums;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case PROCESSING = 'processing';
    case EXPIRED = 'expired';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}