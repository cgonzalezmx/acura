<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class AddressService
{
    public function getAddress(Model $model)
    {
        return join(', ',[
            $model->address,
            'Col: ' . $model->neighborhood,
            'C.P.: ' . $model->zip_code,
            $model->city,
            $model->state
        ]);
    }
}
