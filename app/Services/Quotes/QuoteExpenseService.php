<?php

namespace App\Services\Quotes;

use App\Models\Quotes\Quote;

class QuoteExpenseService {
    public function getUnitaryCost(Quote $quote)
    {
        $expenses = $quote->expenses;
        return $expenses->reduce(function($carry, $expense) {
            return $carry + ($expense->quantity * $expense->cost);
        });
    }

    public function getTotalCost(Quote $quote)
    {
        $unitaryCost = $this->getUnitaryCost($quote);
        return $unitaryCost * $quote->global_expenses_quantity;
    }

    public function count(Quote $quote)
    {
        $quote->loadCount('expenses');
        return $quote->expenses_count;
    }
}
