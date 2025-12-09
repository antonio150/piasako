<?php
namespace App\Enum;

enum DurationUnit: string
{
    case HOURS = 'heures';
    case DAYS = 'jours';
    case WEEKS = 'semaines';
    case MONTHS = 'mois';
}