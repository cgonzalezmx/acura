<?php

namespace App\Traits;

trait ResolvesNumericExpression
{
    protected function resolveNumericExpession(string | null $expression, float $step = 0.001): array | false
    {
        if (!$expression) {
            return false;
        }

        $regex = '/^(>|>=|<=|<|)?\s*(\d+(\.\d+)?)/';
        preg_match($regex, $expression, $matches);
        $operator = $matches[1] ?? '';
        $number = (float) $matches[2];

        return match($operator) {
            '<' => [$operator, $number - $step],
            '>' => [$operator, $number + $step],
            default => [$operator, $number]
        };
    }
}
