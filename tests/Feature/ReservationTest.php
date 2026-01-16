<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $reception;
    protected Zone $zone;
    protected Table $table;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->reception = User::factory()->reception()->create();
        $this->zone = Zone::factory()->create(['nom' => 'Zone Test']);
        $this->table = Table::factory()->create([
            'zone_id' => $this->zone->id,
            'capacite' => 6,
            'statut' => 'disponible',
        ]);
    }

    // ========================================
    // TESTS DE PROTECTION DES ROUTES (AUTH)
    // ========================================

    /**
     * Test 1: Un utilisateur non authentifie est redirige vers login
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        // Index
        $response = $this->get(route('reservations.index'));
        $response->assertRedirect(route('login'));

        // Create
        $response = $this->get(route('reservations.create'));
        $response->assertRedirect(route('login'));

        // Store
        $response = $this->post(route('reservations.store'), [
            'client_nom' => 'Test Client',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ]);
        $response->assertRedirect(route('login'));

        $reservation = Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
        ]);

        // Edit
        $response = $this->get(route('reservations.edit', $reservation));
        $response->assertRedirect(route('login'));

        // Update
        $response = $this->put(route('reservations.update', $reservation), [
            'client_nom' => 'Test Client Updated',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
            'statut' => 'confirmee',
        ]);
        $response->assertRedirect(route('login'));

        // Destroy
        $response = $this->delete(route('reservations.destroy', $reservation));
        $response->assertRedirect(route('login'));

        // UpdateStatut
        $response = $this->patch(route('reservations.statut', $reservation), [
            'statut' => 'confirmee',
        ]);
        $response->assertRedirect(route('login'));
    }

    // ========================================
    // TESTS D'AUTORISATION (ADMIN ET RECEPTION)
    // ========================================

    /**
     * Test 2: Un utilisateur admin peut acceder aux reservations
     */
    public function test_admin_can_access_reservations(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reservations.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('reservations.create'));
        $response->assertStatus(200);
    }

    /**
     * Test 3: Un utilisateur reception peut acceder aux reservations
     */
    public function test_reception_can_access_reservations(): void
    {
        $response = $this->actingAs($this->reception)->get(route('reservations.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->reception)->get(route('reservations.create'));
        $response->assertStatus(200);
    }

    // ========================================
    // TESTS CRUD - INDEX
    // ========================================

    /**
     * Test 4: Un utilisateur authentifie peut voir la liste des reservations
     */
    public function test_authenticated_user_can_view_reservations_list(): void
    {
        Reservation::factory()->count(3)->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('reservations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('reservations.index');
        $response->assertViewHas('reservations');
        $response->assertViewHas('stats');
    }

    // ========================================
    // TESTS CRUD - CREATE
    // ========================================

    /**
     * Test 5: Un utilisateur peut voir le formulaire de creation
     */
    public function test_user_can_view_create_form(): void
    {
        $response = $this->actingAs($this->reception)->get(route('reservations.create'));

        $response->assertStatus(200);
        $response->assertViewIs('reservations.create');
        $response->assertViewHas('tables');
        $response->assertViewHas('selectedDate');
    }

    // ========================================
    // TESTS CRUD - STORE
    // ========================================

    /**
     * Test 6: Un utilisateur peut creer une reservation valide
     */
    public function test_user_can_create_valid_reservation(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
            'commentaire' => 'Anniversaire',
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertRedirect(route('reservations.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reservations', [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'table_id' => $this->table->id,
            'user_id' => $this->reception->id,
            'statut' => 'en_attente',
        ]);
    }

    /**
     * Test 7: La reservation met la table en statut reservee si c'est pour aujourd'hui
     */
    public function test_reservation_today_sets_table_status_to_reservee(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'statut' => 'reservee',
        ]);
    }

    /**
     * Test 8: La reservation ne change pas le statut de la table si c'est pour une date future
     */
    public function test_reservation_future_does_not_change_table_status(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->addDays(3)->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'statut' => 'disponible',
        ]);
    }

    // ========================================
    // TESTS CRUD - EDIT
    // ========================================

    /**
     * Test 9: Un utilisateur peut voir le formulaire d'edition
     */
    public function test_user_can_view_edit_form(): void
    {
        $reservation = Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->reception)->get(route('reservations.edit', $reservation));

        $response->assertStatus(200);
        $response->assertViewIs('reservations.edit');
        $response->assertViewHas('reservation');
        $response->assertViewHas('tables');
    }

    // ========================================
    // TESTS CRUD - UPDATE
    // ========================================

    /**
     * Test 10: Un utilisateur peut modifier une reservation
     */
    public function test_user_can_update_reservation(): void
    {
        $reservation = Reservation::factory()->create([
            'client_nom' => 'Jean Dupont',
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'heure_reservation' => '19:00',
        ]);

        $updateData = [
            'client_nom' => 'Pierre Martin',
            'client_telephone' => '0698765432',
            'nombre_personnes' => 6,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '20:00',
            'table_id' => $this->table->id,
            'statut' => 'confirmee',
            'commentaire' => 'VIP',
        ];

        $response = $this->actingAs($this->reception)->put(route('reservations.update', $reservation), $updateData);

        $response->assertRedirect(route('reservations.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'client_nom' => 'Pierre Martin',
            'client_telephone' => '0698765432',
            'nombre_personnes' => 6,
            'statut' => 'confirmee',
        ]);
    }

    // ========================================
    // TESTS CRUD - DESTROY
    // ========================================

    /**
     * Test 11: Un utilisateur peut supprimer une reservation
     */
    public function test_user_can_delete_reservation(): void
    {
        $reservation = Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->reception)->delete(route('reservations.destroy', $reservation));

        $response->assertRedirect(route('reservations.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    /**
     * Test 12: La suppression d'une reservation aujourd'hui libere la table
     */
    public function test_delete_reservation_today_frees_table(): void
    {
        $this->table->update(['statut' => 'reservee']);

        $reservation = Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'statut' => 'en_attente',
        ]);

        $this->actingAs($this->reception)->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'statut' => 'disponible',
        ]);
    }

    // ========================================
    // TESTS DE VALIDATION DES INPUTS
    // ========================================

    /**
     * Test 13: La creation echoue si le nom du client est vide
     */
    public function test_reservation_creation_fails_with_empty_client_nom(): void
    {
        $reservationData = [
            'client_nom' => '',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('client_nom');
        $this->assertDatabaseCount('reservations', 0);
    }

    /**
     * Test 14: La creation echoue si le telephone est vide
     */
    public function test_reservation_creation_fails_with_empty_client_telephone(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('client_telephone');
        $this->assertDatabaseCount('reservations', 0);
    }

    /**
     * Test 15: La creation echoue si le nombre de personnes est inferieur a 1
     */
    public function test_reservation_creation_fails_with_nombre_personnes_below_minimum(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 0,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('nombre_personnes');
        $this->assertDatabaseCount('reservations', 0);
    }

    /**
     * Test 16: La creation echoue si le nombre de personnes depasse 20
     */
    public function test_reservation_creation_fails_with_nombre_personnes_above_maximum(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 21,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('nombre_personnes');
        $this->assertDatabaseCount('reservations', 0);
    }

    /**
     * Test 17: La creation echoue si la date est dans le passe
     */
    public function test_reservation_creation_fails_with_past_date(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->subDays(1)->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('date_reservation');
        $this->assertDatabaseCount('reservations', 0);
    }

    /**
     * Test 18: La creation echoue si le format de l'heure est invalide
     */
    public function test_reservation_creation_fails_with_invalid_time_format(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '7pm',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('heure_reservation');
        $this->assertDatabaseCount('reservations', 0);
    }

    /**
     * Test 19: La creation echoue si la table n'existe pas
     */
    public function test_reservation_creation_fails_with_nonexistent_table(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => 9999,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('table_id');
        $this->assertDatabaseCount('reservations', 0);
    }

    /**
     * Test 20: La creation echoue si le nombre de personnes depasse la capacite de la table
     */
    public function test_reservation_creation_fails_when_exceeding_table_capacity(): void
    {
        $reservationData = [
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 10, // Table a capacite de 6
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('nombre_personnes');
        $this->assertDatabaseCount('reservations', 0);
    }

    // ========================================
    // TESTS DE DETECTION DES CONFLITS
    // ========================================

    /**
     * Test 21: La creation echoue si la table est deja reservee pour le meme creneau
     */
    public function test_reservation_creation_fails_with_conflict_same_time(): void
    {
        // Creer une reservation existante
        Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'heure_reservation' => '19:00',
            'statut' => 'en_attente',
        ]);

        // Tenter de creer une reservation au meme moment
        $reservationData = [
            'client_nom' => 'Nouveau Client',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('table_id');
        $this->assertDatabaseCount('reservations', 1);
    }

    /**
     * Test 22: La creation echoue si la table est reservee dans la plage de 2 heures
     */
    public function test_reservation_creation_fails_with_conflict_within_2_hours(): void
    {
        // Creer une reservation existante a 19h
        Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'heure_reservation' => '19:00',
            'statut' => 'en_attente',
        ]);

        // Tenter de creer une reservation a 20h (dans la plage de 2h)
        $reservationData = [
            'client_nom' => 'Nouveau Client',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '20:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertSessionHasErrors('table_id');
        $this->assertDatabaseCount('reservations', 1);
    }

    /**
     * Test 23: La creation reussit si la reservation est en dehors de la plage de 2 heures
     */
    public function test_reservation_creation_succeeds_outside_2_hours_window(): void
    {
        // Creer une reservation existante a 12h
        Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'heure_reservation' => '12:00',
            'statut' => 'en_attente',
        ]);

        // Creer une reservation a 19h (plus de 2h d'ecart)
        $reservationData = [
            'client_nom' => 'Nouveau Client',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseCount('reservations', 2);
    }

    /**
     * Test 24: La creation reussit si la reservation existante est annulee
     */
    public function test_reservation_creation_succeeds_when_existing_is_cancelled(): void
    {
        // Creer une reservation annulee
        Reservation::factory()->annulee()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'heure_reservation' => '19:00',
        ]);

        // Creer une nouvelle reservation au meme moment
        $reservationData = [
            'client_nom' => 'Nouveau Client',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseCount('reservations', 2);
    }

    /**
     * Test 25: La creation reussit si la reservation est pour un autre jour
     */
    public function test_reservation_creation_succeeds_on_different_day(): void
    {
        // Creer une reservation pour aujourd'hui
        Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'heure_reservation' => '19:00',
            'statut' => 'en_attente',
        ]);

        // Creer une reservation pour demain a la meme heure
        $reservationData = [
            'client_nom' => 'Nouveau Client',
            'client_telephone' => '0612345678',
            'nombre_personnes' => 4,
            'date_reservation' => today()->addDay()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
        ];

        $response = $this->actingAs($this->reception)->post(route('reservations.store'), $reservationData);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseCount('reservations', 2);
    }

    /**
     * Test 26: La modification detecte les conflits avec d'autres reservations
     */
    public function test_reservation_update_detects_conflicts(): void
    {
        // Creer une premiere reservation a 19h
        $existingReservation = Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'heure_reservation' => '19:00',
            'statut' => 'en_attente',
        ]);

        // Creer une deuxieme reservation a 12h
        $reservationToUpdate = Reservation::factory()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'heure_reservation' => '12:00',
            'statut' => 'en_attente',
        ]);

        // Tenter de deplacer la 2eme a 19h (conflit)
        $updateData = [
            'client_nom' => $reservationToUpdate->client_nom,
            'client_telephone' => $reservationToUpdate->client_telephone,
            'nombre_personnes' => $reservationToUpdate->nombre_personnes,
            'date_reservation' => today()->format('Y-m-d'),
            'heure_reservation' => '19:00',
            'table_id' => $this->table->id,
            'statut' => 'en_attente',
        ];

        $response = $this->actingAs($this->reception)->put(route('reservations.update', $reservationToUpdate), $updateData);

        $response->assertSessionHasErrors('table_id');
    }

    // ========================================
    // TESTS DU CHANGEMENT DE STATUT RAPIDE
    // ========================================

    /**
     * Test 27: Un utilisateur peut changer le statut d'une reservation vers confirmee
     */
    public function test_user_can_change_status_to_confirmee(): void
    {
        $reservation = Reservation::factory()->enAttente()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $response = $this->actingAs($this->reception)->patch(route('reservations.statut', $reservation), [
            'statut' => 'confirmee',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'statut' => 'confirmee',
        ]);
    }

    /**
     * Test 28: Un utilisateur peut changer le statut d'une reservation vers terminee
     */
    public function test_user_can_change_status_to_terminee(): void
    {
        $reservation = Reservation::factory()->confirmee()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $response = $this->actingAs($this->reception)->patch(route('reservations.statut', $reservation), [
            'statut' => 'terminee',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'statut' => 'terminee',
        ]);
    }

    /**
     * Test 29: Un utilisateur peut changer le statut d'une reservation vers annulee
     */
    public function test_user_can_change_status_to_annulee(): void
    {
        $reservation = Reservation::factory()->enAttente()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $response = $this->actingAs($this->reception)->patch(route('reservations.statut', $reservation), [
            'statut' => 'annulee',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'statut' => 'annulee',
        ]);
    }

    /**
     * Test 30: Le changement de statut echoue avec un statut invalide
     */
    public function test_status_change_fails_with_invalid_status(): void
    {
        $reservation = Reservation::factory()->enAttente()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->reception)->patch(route('reservations.statut', $reservation), [
            'statut' => 'invalide',
        ]);

        $response->assertSessionHasErrors('statut');
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'statut' => 'en_attente',
        ]);
    }

    // ========================================
    // TESTS DE GESTION AUTOMATIQUE DU STATUT DES TABLES
    // ========================================

    /**
     * Test 31: Confirmer une reservation (client arrive) met la table en occupee
     */
    public function test_confirming_reservation_sets_table_to_occupee(): void
    {
        $this->table->update(['statut' => 'reservee']);

        $reservation = Reservation::factory()->enAttente()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $this->actingAs($this->reception)->patch(route('reservations.statut', $reservation), [
            'statut' => 'confirmee',
        ]);

        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'statut' => 'occupee',
        ]);
    }

    /**
     * Test 32: Terminer une reservation libere la table
     */
    public function test_completing_reservation_frees_table(): void
    {
        $this->table->update(['statut' => 'occupee']);

        $reservation = Reservation::factory()->confirmee()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $this->actingAs($this->reception)->patch(route('reservations.statut', $reservation), [
            'statut' => 'terminee',
        ]);

        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'statut' => 'disponible',
        ]);
    }

    /**
     * Test 33: Annuler une reservation libere la table
     */
    public function test_cancelling_reservation_frees_table(): void
    {
        $this->table->update(['statut' => 'reservee']);

        $reservation = Reservation::factory()->enAttente()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $this->actingAs($this->reception)->patch(route('reservations.statut', $reservation), [
            'statut' => 'annulee',
        ]);

        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'statut' => 'disponible',
        ]);
    }

    /**
     * Test 34: Le changement de statut ne modifie pas la table si la reservation n'est pas aujourd'hui
     */
    public function test_status_change_does_not_affect_table_for_future_reservation(): void
    {
        $this->table->update(['statut' => 'disponible']);

        $reservation = Reservation::factory()->enAttente()->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today()->addDays(3),
        ]);

        $this->actingAs($this->reception)->patch(route('reservations.statut', $reservation), [
            'statut' => 'confirmee',
        ]);

        // La table reste disponible car la reservation est future
        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'statut' => 'disponible',
        ]);
    }

    // ========================================
    // TESTS DES FILTRES
    // ========================================

    /**
     * Test 35: Filtrage des reservations par date
     */
    public function test_reservations_can_be_filtered_by_date(): void
    {
        // Reservations pour aujourd'hui
        Reservation::factory()->count(2)->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        // Reservations pour demain
        Reservation::factory()->count(3)->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today()->addDay(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('reservations.index', [
            'date' => today()->addDay()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $reservations = $response->viewData('reservations');
        $this->assertCount(3, $reservations);
    }

    /**
     * Test 36: Filtrage des reservations par statut
     */
    public function test_reservations_can_be_filtered_by_statut(): void
    {
        Reservation::factory()->enAttente()->count(2)->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        Reservation::factory()->confirmee()->count(1)->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('reservations.index', [
            'statut' => 'en_attente',
        ]));

        $response->assertStatus(200);
        $reservations = $response->viewData('reservations');
        $this->assertCount(2, $reservations);
        $this->assertTrue($reservations->every(fn ($r) => $r->statut === 'en_attente'));
    }

    /**
     * Test 37: Recherche des reservations par nom client
     */
    public function test_reservations_can_be_searched_by_client_name(): void
    {
        Reservation::factory()->create([
            'client_nom' => 'Jean Dupont',
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        Reservation::factory()->create([
            'client_nom' => 'Pierre Martin',
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('reservations.index', [
            'recherche' => 'Dupont',
        ]));

        $response->assertStatus(200);
        $reservations = $response->viewData('reservations');
        $this->assertCount(1, $reservations);
        $this->assertEquals('Jean Dupont', $reservations->first()->client_nom);
    }

    /**
     * Test 38: Recherche des reservations par telephone client
     */
    public function test_reservations_can_be_searched_by_client_phone(): void
    {
        Reservation::factory()->create([
            'client_nom' => 'Jean Dupont',
            'client_telephone' => '0612345678',
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        Reservation::factory()->create([
            'client_nom' => 'Pierre Martin',
            'client_telephone' => '0698765432',
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('reservations.index', [
            'recherche' => '0698',
        ]));

        $response->assertStatus(200);
        $reservations = $response->viewData('reservations');
        $this->assertCount(1, $reservations);
        $this->assertEquals('Pierre Martin', $reservations->first()->client_nom);
    }

    /**
     * Test 39: Le filtrage avec un statut invalide retourne une erreur de validation
     */
    public function test_filter_with_invalid_statut_returns_validation_error(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reservations.index', ['statut' => 'invalide']));

        $response->assertSessionHasErrors('statut');
    }

    /**
     * Test 40: Les statistiques sont correctement calculees
     */
    public function test_stats_are_correctly_calculated(): void
    {
        Reservation::factory()->enAttente()->count(3)->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'nombre_personnes' => 2,
        ]);

        Reservation::factory()->confirmee()->count(2)->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'nombre_personnes' => 4,
        ]);

        Reservation::factory()->annulee()->count(1)->create([
            'table_id' => $this->table->id,
            'user_id' => $this->admin->id,
            'date_reservation' => today(),
            'nombre_personnes' => 3,
        ]);

        $response = $this->actingAs($this->admin)->get(route('reservations.index'));

        $response->assertStatus(200);
        $stats = $response->viewData('stats');

        $this->assertEquals(6, $stats['total']);
        $this->assertEquals(3, $stats['en_attente']);
        $this->assertEquals(2, $stats['confirmee']);
        // Personnes = 3*2 (en_attente) + 2*4 (confirmee) = 6 + 8 = 14
        $this->assertEquals(14, $stats['personnes']);
    }
}
