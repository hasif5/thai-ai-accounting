<?php

namespace App\Enums;

enum PurchaseStatus: string
{
    case DRAFT = 'draft';
    case APPROVED = 'approved';
    case PAID = 'paid';
}