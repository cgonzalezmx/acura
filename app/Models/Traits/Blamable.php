<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait Blamable {
    protected static function bootBlamable(): void
    {
        static::creating(function($model) {
            $model->created_by = Auth::id();
        });

        static::updating(function($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}