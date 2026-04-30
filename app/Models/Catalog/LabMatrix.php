<?php

namespace App\Models\Catalog;

use App\Models\Parameter;
use App\Models\Traits\Blamable;
use App\Models\Traits\SerializeDate;
use App\Models\Traits\Versioning;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMatrix extends Model
{
    use SoftDeletes, Blamable, SerializeDate, Versioning;

    protected $table = 'lab_matrices';
    protected $fillable = ['name', 'code'];

    public function parameters(): HasMany
    {
        return $this->hasMany(Parameter::class, 'lab_matrix_id');
    }
}
