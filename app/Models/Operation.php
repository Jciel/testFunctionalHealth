<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'type', 'account_id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
