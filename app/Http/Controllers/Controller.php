<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class Controller
{
    protected array $attributes;
    protected string $modelClass;
    protected array $relations;

    protected function save(Model $model, Request $request)
    {
        $attributes = $request->only($this->attributes);

        foreach($attributes as $attr => $val) {
            $model->setAttribute($attr, $val);
        }

        $model->save();
    }
}
