<?php

namespace App\Support;

use Carbon\CarbonInterface;

final class SchoolCredentials
{
    /**
     * Default first-time password: date of birth as DDMMYYYY (e.g. 15 March 2010 → 15032010).
     */
    public static function plainPasswordFromBirthDate(CarbonInterface $date): string
    {
        return $date->format('dmY');
    }
}
