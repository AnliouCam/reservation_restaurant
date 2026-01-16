<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'capacite',
        'statut',
        'zone_id',
    ];

    /**
     * Relation : une table appartient a une zone
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Relation : une table a plusieurs reservations
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
