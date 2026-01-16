<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_nom',
        'client_telephone',
        'nombre_personnes',
        'date_reservation',
        'heure_reservation',
        'table_id',
        'statut',
        'commentaire',
        'user_id',
    ];

    protected $casts = [
        'date_reservation' => 'date',
        'heure_reservation' => 'datetime:H:i',
    ];

    /**
     * Statuts possibles pour une reservation
     */
    public const STATUTS = [
        'en_attente' => 'En attente',
        'confirmee' => 'Confirmee',
        'terminee' => 'Terminee',
        'annulee' => 'Annulee',
    ];

    /**
     * Relation : une reservation appartient a une table
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Relation : une reservation a ete creee par un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope : reservations du jour
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date_reservation', today());
    }

    /**
     * Scope : reservations futures
     */
    public function scopeFutures($query)
    {
        return $query->where('date_reservation', '>=', today());
    }

    /**
     * Scope : reservations actives (non annulees, non terminees)
     */
    public function scopeActives($query)
    {
        return $query->whereIn('statut', ['en_attente', 'confirmee']);
    }
}
