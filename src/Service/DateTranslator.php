<?php

namespace App\Service;

use Twig\Node\Expression\ConstantExpression;

class DateTranslator
{
    public static function translate(string $date)
    {
        $englishMonth = ['January', 'Jan', 'February', 'Feb', 'March', 'Mar', 'April', 'Apr', 'May', 'June', 'Jun', 'July', 'Jul', 'August', 'Aug', 'September', 'Sep', 'October', 'Oct', 'November', 'Nov', 'December', 'Dec'];

        $frenchMonth = ['janvier', 'jan', 'février', 'fév', 'mars', 'mar', 'avril', 'avr', 'mai', 'juin', 'juin', 'juillet', 'juil', 'août', 'août', 'septembre', 'sep', 'octobre', 'oct', 'novembre', 'nov', 'décembre', 'déc'];

        return str_replace($englishMonth, $frenchMonth, $date);
    }
}