<?php

namespace App\Models\Catalog;

use App\Models\Traits\Blamable;
use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterCategory extends Model
{
    use SoftDeletes, Blamable, SerializeDate;

    protected $fillable = ['name', 'description'];
}
