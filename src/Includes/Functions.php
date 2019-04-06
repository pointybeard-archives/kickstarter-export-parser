<?php

if(!function_exists("cleanKickstarterCurrencyAmount")) {
    function cleanKickstarterCurrencyAmount($amount) : float {
        return (float)preg_replace("@^[^\d]+@", '', $amount);
    }
}

if(!function_exists("array_merge_sum")) {
    function array_merge_sum(array $array1, array $array2) : array
    {
        $keys = array_unique(array_merge(array_keys($array1), array_keys($array2)));
        $result = [];
        foreach($keys as $k) {
            $result[$k] =
                (isset($array1[$k]) ? $array1[$k] : 0.0) +
                (isset($array2[$k]) ? $array2[$k] : 0.0)
            ;
        }
        return $result;
    }
}

if(!function_exists("formatNumberToSafeCurrency")) {
    function formatNumberToSafeCurrency($number)
    {
        // The point of this method is to remove strange leftover floating point
        // noise. E.g. even though the number might be 0, its actually
        // -7.105427357601E-15. This screws up comparisons since 0.0 does not equal
        // -7.105427357601E-15. Using number_format rectifies this problem.
        $number = (float)number_format((float)$number, 2, '.', '');

        // Sometimes it's possible to end up with a -0.0 value. This is because
        // for example, -7.105427357601E-15 put through number_format will produce
        // -0.0
        if ($number == -0.0) {
            $number = +0.0;
        }

        return $number;
    }
}
