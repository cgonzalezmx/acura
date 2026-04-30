<?php

namespace App\Models\Catalog;

use App\Models\Traits\Blamable;
use App\Models\Traits\SerializeDate;
use App\Models\Traits\Versioning;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SamplingRemark extends Model
{
    use SoftDeletes, Blamable, SerializeDate, Versioning;

    protected $fillable = ['code', 'description'];
}
