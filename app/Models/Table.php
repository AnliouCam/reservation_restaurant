<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
