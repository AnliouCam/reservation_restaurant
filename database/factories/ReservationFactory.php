<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_nom' => fake()->name(),
            'client_telephone' => fake()->phoneNumber(),
            'nombre_personnes' => fake()->numberBetween(1, 8),
            'date_reservation' => today(),
            'heure_reservation' => fake()->time('H:i'),
            'table_id' => Table::factory(),
            'user_id' => User::factory(),
            'statut' => 'en_attente',
            'commentaire' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Reservation en attente
     */
    public function enAttente(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_attente',
        ]);
    }

    /**
     * Reservation confirmee
     */
    public function confirmee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'confirmee',
        ]);
    }

    /**
     * Reservation terminee
     */
    public function terminee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'terminee',
        ]);
    }

    /**
     * Reservation annulee
     */
    public function annulee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'annulee',
        ]);
    }

    /**
     * Reservation pour aujourd'hui
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_reservation' => today(),
        ]);
    }

    /**
     * Reservation future
     */
    public function future(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_reservation' => today()->addDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
