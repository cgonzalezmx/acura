<?php

namespace App\Models\Quotes;

use App\Models\Client\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelectedContact extends Model
{
    public $timestamps = false;
    protected $table = 'quote_selected_contacts';
    protected $fillable = [
        'quote_id',
        'client_contact_id'
    ];

    public function systemContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'client_contact_id');
    }
}
