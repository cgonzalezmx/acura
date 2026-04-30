<?php

namespace App\Services\Regulatory\Structure;

use App\Models\Regulatory\Structure\Bundle;

class BundleService
{
    public function createBundle(array $data)
    {
        $bundle = new Bundle();
        $bundle->takes = $data['takes'] ?? 1;
        $bundle->price = $data['price'] ?? 0;
        return $bundle;
    }
}