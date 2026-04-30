<?php

namespace App\Services\Formats;

class RowsService
{
    public function totalRowsSizeByText(string $text, int $textInRowWidth, float $step = 0): float
    {
        $stringLength = mb_strlen($text);
        $size = ceil($stringLength / $textInRowWidth);

        return $step === 0 ? $size : $size * $step;
    }
}