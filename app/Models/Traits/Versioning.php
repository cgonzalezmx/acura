<?php

namespace App\Models\Traits;

trait Versioning {

    public static function bootVersioning(): void
    {
        static::updating(function($model) {
            $model->version += 1;
        });
    }
}