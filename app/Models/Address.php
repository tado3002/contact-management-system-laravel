<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $table = 'addresses';
    protected $incrementing = true;
    protected $timestamps = true;

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'address_id', 'id');
    }
}
