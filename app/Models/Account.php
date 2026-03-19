<?php

namespace App\Models;

use App\Casts\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $casts = ['balance' => Money::class . ':balance,currency'];

    protected $fillable = ['id', 'number', 'balance', 'currency'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }
}
