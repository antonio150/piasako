<?php

namespace App\Enum;

enum ShiftType: string
{
    case JOUR = 'jour';
    case NUIT = 'nuit';
    case BOTH = 'both';
}