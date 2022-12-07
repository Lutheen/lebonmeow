<?php

namespace App\Service;

use Twig\Node\Expression\ConstantExpression;

class DateTranslator
{
    public static function translate(string $date)
    {
        $englishMonth = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $frenchMonth = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

        return str_replace($englishMonth, $frenchMonth, $date);
    }
}