<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Merchant extends Model
{
    protected $fillable = ['name', 'api_key', 'webhook_url'];

    public static function generateApiKey(): string
    {
        return 'mk_' . Str::random(32);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
