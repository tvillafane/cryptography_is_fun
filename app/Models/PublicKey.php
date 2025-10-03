<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicKey extends Model
{
    public function nonces()
    {
        return $this->hasMany(Nonce::class);
    }

    public $guarded = [];
}
