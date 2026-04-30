<?php

namespace App\Traits;

trait ConvertIndexToLetter
{
    private function getLetterIndex(int $index): string
    {
        $result = '';
        $n = $index;

        while($n > 0) {
            $n--;
            $remainder = $n % 26;
            $result = chr(65 + $remainder) . $result;
            $n = intdiv($n, 26);
        }

        return $result;
    }

}