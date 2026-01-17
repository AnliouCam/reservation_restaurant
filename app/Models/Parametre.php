<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Parametre extends Model
{
    protected $fillable = [
        'cle',
        'valeur',
        'type',
        'description',
    ];

    /**
     * Recupere la valeur d'un parametre par sa cle
     */
    public static function get(string $cle, mixed $default = null): mixed
    {
        $parametre = Cache::remember("parametre_{$cle}", 3600, function () use ($cle) {
            return static::where('cle', $cle)->first();
        });

        if (!$parametre) {
            return $default;
        }

        return static::castValue($parametre->valeur, $parametre->type);
    }

    /**
     * Definit la valeur d'un parametre
     */
    public static function set(string $cle, mixed $valeur, string $type = 'string', ?string $description = null): void
    {
        $valeurStockee = $type === 'json' ? json_encode($valeur) : (string) $valeur;

        static::updateOrCreate(
            ['cle' => $cle],
            [
                'valeur' => $valeurStockee,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("parametre_{$cle}");
    }

    /**
     * Cast la valeur selon son type
     */
    protected static function castValue(?string $valeur, string $type): mixed
    {
        if ($valeur === null) {
            return null;
        }

        return match ($type) {
            'json' => json_decode($valeur, true),
            'boolean' => filter_var($valeur, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $valeur,
            default => $valeur,
        };
    }

    /**
     * Parametres par defaut du restaurant
     */
    public static function getDefaults(): array
    {
        return [
            'nom_restaurant' => [
                'valeur' => 'Mon Restaurant',
                'type' => 'string',
                'description' => 'Nom du restaurant',
            ],
            'telephone' => [
                'valeur' => '',
                'type' => 'string',
                'description' => 'Telephone du restaurant',
            ],
            'adresse' => [
                'valeur' => '',
                'type' => 'string',
                'description' => 'Adresse du restaurant',
            ],
            'email' => [
                'valeur' => '',
                'type' => 'string',
                'description' => 'Email de contact',
            ],
            'horaires' => [
                'valeur' => json_encode([
                    'lundi' => ['ouvert' => true, 'midi' => '12:00-14:00', 'soir' => '19:00-22:00'],
                    'mardi' => ['ouvert' => true, 'midi' => '12:00-14:00', 'soir' => '19:00-22:00'],
                    'mercredi' => ['ouvert' => true, 'midi' => '12:00-14:00', 'soir' => '19:00-22:00'],
                    'jeudi' => ['ouvert' => true, 'midi' => '12:00-14:00', 'soir' => '19:00-22:00'],
                    'vendredi' => ['ouvert' => true, 'midi' => '12:00-14:00', 'soir' => '19:00-22:00'],
                    'samedi' => ['ouvert' => true, 'midi' => '12:00-14:00', 'soir' => '19:00-22:00'],
                    'dimanche' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                ]),
                'type' => 'json',
                'description' => 'Horaires d\'ouverture',
            ],
            'duree_reservation' => [
                'valeur' => '120',
                'type' => 'integer',
                'description' => 'Duree moyenne d\'une reservation en minutes',
            ],
        ];
    }

    /**
     * Initialise les parametres par defaut
     */
    public static function initDefaults(): void
    {
        foreach (static::getDefaults() as $cle => $config) {
            if (!static::where('cle', $cle)->exists()) {
                static::create([
                    'cle' => $cle,
                    'valeur' => $config['valeur'],
                    'type' => $config['type'],
                    'description' => $config['description'],
                ]);
            }
        }
    }
}
