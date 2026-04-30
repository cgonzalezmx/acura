<?php

namespace App\Services;

class Pdf
{
    public function reduceFontSize(string $text, int $threshold, string $reducedSizeCss): string
    {
        if (strlen($text) >= $threshold) {
            return <<<HTML
                <span style="font-size: $reducedSizeCss">$text</span>
            HTML;
        }

        return $text;
    }
}