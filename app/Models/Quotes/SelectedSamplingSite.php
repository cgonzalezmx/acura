<?php

namespace App\Models\Quotes;

use Illuminate\Database\Eloquent\Model;

class SelectedSamplingSite extends Model
{
    public $timestamps = false;
    protected $table = 'quote_selected_sampling_sites';
    protected $fillable = [
        'client_sampling_site_id',
        'name',
        'industry_sector',
        'is_main_site',
        'address',
        'neighborhood',
        'city',
        'state',
        'phone',
        'zip_code',
        'contact_name',
    ];
}
