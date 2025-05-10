<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case ISSUED = 'issued';
    case PAID = 'paid';
    case VOID = 'void';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::ISSUED => 'Issued',
            self::PAID => 'Paid',
            self::VOID => 'Void',
        };
    }
}