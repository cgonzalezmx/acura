<?php

namespace App\Models\Quotes;

use App\Models\Client\Client as SystemClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $table = 'quote_client_records';
    public $timestamps = false;
    protected $fillable = [
        'quote_id',
        'name',
        'industry_sector',
        'address',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'client_id'
    ];

    public function systemInfo(): BelongsTo
    {
        return $this->belongsTo(SystemClient::class, 'client_id');
    }
}
