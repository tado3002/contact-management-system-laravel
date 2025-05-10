<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model implements Authenticatable
{
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $table = 'addresses';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'street',
        'city',
        'province',
        'country',
        'contact_id',
        'postal_code'
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'address_id', 'id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthIdentifier()
    {
        return $this->username;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->token;
    }

    public function setRememberToken($value)
    {
        $this->token = $value;
    }

    public function getRememberTokenName()
    {
        return 'token';
    }
    public function getAuthPasswordName()
    {
        return 'passcode';
    }
}
