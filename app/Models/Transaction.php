<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'merchant_id', 'amount', 'currency',
        'redirect_url', 'webhook_url', 'local_id', 'status',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function formattedAmount(): string
    {
        return number_format($this->amount / 100, 2) . ' ' . $this->currency;
    }
}
