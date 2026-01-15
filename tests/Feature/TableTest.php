<?php

namespace Tests\Feature;

use App\Models\Table;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $reception;
    protected Zone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->reception = User::factory()->reception()->create();
        $this->zone = Zone::factory()->create(['nom' => 'Zone Test']);
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
        $response = $this->get(route('tables.index'));
        $response->assertRedirect(route('login'));

        // Create
        $response = $this->get(route('tables.create'));
        $response->assertRedirect(route('login'));

        // Store
        $response = $this->post(route('tables.store'), [
            'numero' => 'T1',
            'capacite' => 4,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ]);
        $response->assertRedirect(route('login'));

        $table = Table::factory()->create(['zone_id' => $this->zone->id]);

        // Edit
        $response = $this->get(route('tables.edit', $table));
        $response->assertRedirect(route('login'));

        // Update
        $response = $this->put(route('tables.update', $table), [
            'numero' => 'T2',
            'capacite' => 6,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ]);
        $response->assertRedirect(route('login'));

        // Destroy
        $response = $this->delete(route('tables.destroy', $table));
        $response->assertRedirect(route('login'));

        // UpdateStatut
        $response = $this->patch(route('tables.statut', $table), [
            'statut' => 'occupee',
        ]);
        $response->assertRedirect(route('login'));
    }

    // ========================================
    // TESTS D'AUTORISATION (ROLE ADMIN ONLY)
    // ========================================

    /**
     * Test 2: Un utilisateur reception ne peut pas acceder aux tables (403)
     */
    public function test_reception_user_cannot_access_tables(): void
    {
        // Index
        $response = $this->actingAs($this->reception)->get(route('tables.index'));
        $response->assertStatus(403);

        // Create
        $response = $this->actingAs($this->reception)->get(route('tables.create'));
        $response->assertStatus(403);

        // Store
        $response = $this->actingAs($this->reception)->post(route('tables.store'), [
            'numero' => 'T1',
            'capacite' => 4,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ]);
        $response->assertStatus(403);

        $table = Table::factory()->create(['zone_id' => $this->zone->id]);

        // Edit
        $response = $this->actingAs($this->reception)->get(route('tables.edit', $table));
        $response->assertStatus(403);

        // Update
        $response = $this->actingAs($this->reception)->put(route('tables.update', $table), [
            'numero' => 'T2',
            'capacite' => 6,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ]);
        $response->assertStatus(403);

        // Destroy
        $response = $this->actingAs($this->reception)->delete(route('tables.destroy', $table));
        $response->assertStatus(403);

        // UpdateStatut
        $response = $this->actingAs($this->reception)->patch(route('tables.statut', $table), [
            'statut' => 'occupee',
        ]);
        $response->assertStatus(403);
    }

    // ========================================
    // TESTS CRUD - INDEX
    // ========================================

    /**
     * Test 3: Un admin peut voir la liste des tables
     */
    public function test_admin_can_view_tables_list(): void
    {
        Table::factory()->count(3)->create(['zone_id' => $this->zone->id]);

        $response = $this->actingAs($this->admin)->get(route('tables.index'));

        $response->assertStatus(200);
        $response->assertViewIs('tables.index');
        $response->assertViewHas('tables');
        $response->assertViewHas('zones');
        $response->assertViewHas('stats');
    }

    // ========================================
    // TESTS CRUD - CREATE
    // ========================================

    /**
     * Test 4: Un admin peut voir le formulaire de creation
     */
    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('tables.create'));

        $response->assertStatus(200);
        $response->assertViewIs('tables.create');
        $response->assertViewHas('zones');
    }

    /**
     * Test 5: Redirection si aucune zone n'existe
     */
    public function test_create_redirects_when_no_zones_exist(): void
    {
        // Supprimer toutes les zones
        Zone::query()->delete();

        $response = $this->actingAs($this->admin)->get(route('tables.create'));

        $response->assertRedirect(route('tables.index'));
        $response->assertSessionHas('error');
    }

    // ========================================
    // TESTS CRUD - STORE
    // ========================================

    /**
     * Test 6: Un admin peut creer une table valide
     */
    public function test_admin_can_create_valid_table(): void
    {
        $tableData = [
            'numero' => 'T1',
            'capacite' => 4,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ];

        $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

        $response->assertRedirect(route('tables.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tables', [
            'numero' => 'T1',
            'capacite' => 4,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ]);
    }

    // ========================================
    // TESTS CRUD - EDIT
    // ========================================

    /**
     * Test 7: Un admin peut voir le formulaire d'edition
     */
    public function test_admin_can_view_edit_form(): void
    {
        $table = Table::factory()->create(['zone_id' => $this->zone->id]);

        $response = $this->actingAs($this->admin)->get(route('tables.edit', $table));

        $response->assertStatus(200);
        $response->assertViewIs('tables.edit');
        $response->assertViewHas('table');
        $response->assertViewHas('zones');
    }

    // ========================================
    // TESTS CRUD - UPDATE
    // ========================================

    /**
     * Test 8: Un admin peut modifier une table
     */
    public function test_admin_can_update_table(): void
    {
        $table = Table::factory()->create([
            'numero' => 'T1',
            'capacite' => 4,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ]);

        $updateData = [
            'numero' => 'T99',
            'capacite' => 8,
            'zone_id' => $this->zone->id,
            'statut' => 'reservee',
        ];

        $response = $this->actingAs($this->admin)->put(route('tables.update', $table), $updateData);

        $response->assertRedirect(route('tables.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'numero' => 'T99',
            'capacite' => 8,
            'statut' => 'reservee',
        ]);
        $this->assertDatabaseMissing('tables', [
            'id' => $table->id,
            'numero' => 'T1',
        ]);
    }

    // ========================================
    // TESTS CRUD - DESTROY
    // ========================================

    /**
     * Test 9: Un admin peut supprimer une table
     */
    public function test_admin_can_delete_table(): void
    {
        $table = Table::factory()->create([
            'numero' => 'T-DELETE',
            'zone_id' => $this->zone->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('tables.destroy', $table));

        $response->assertRedirect(route('tables.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('tables', ['id' => $table->id]);
    }

    // ========================================
    // TESTS DE VALIDATION DES INPUTS
    // ========================================

    /**
     * Test 10: La creation echoue si le numero est vide
     */
    public function test_table_creation_fails_with_empty_numero(): void
    {
        $tableData = [
            'numero' => '',
            'capacite' => 4,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ];

        $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

        $response->assertSessionHasErrors('numero');
        $this->assertDatabaseCount('tables', 0);
    }

    /**
     * Test 11: La creation echoue si la capacite est inferieure a 1
     */
    public function test_table_creation_fails_with_capacite_below_minimum(): void
    {
        $tableData = [
            'numero' => 'T1',
            'capacite' => 0,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ];

        $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

        $response->assertSessionHasErrors('capacite');
        $this->assertDatabaseCount('tables', 0);
    }

    /**
     * Test 12: La creation echoue si la capacite depasse 20
     */
    public function test_table_creation_fails_with_capacite_above_maximum(): void
    {
        $tableData = [
            'numero' => 'T1',
            'capacite' => 21,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ];

        $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

        $response->assertSessionHasErrors('capacite');
        $this->assertDatabaseCount('tables', 0);
    }

    /**
     * Test 13: La creation echoue si la zone n'existe pas
     */
    public function test_table_creation_fails_with_nonexistent_zone(): void
    {
        $tableData = [
            'numero' => 'T1',
            'capacite' => 4,
            'zone_id' => 9999,
            'statut' => 'disponible',
        ];

        $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

        $response->assertSessionHasErrors('zone_id');
        $this->assertDatabaseCount('tables', 0);
    }

    /**
     * Test 14: La creation echoue si le statut est invalide
     */
    public function test_table_creation_fails_with_invalid_statut(): void
    {
        $tableData = [
            'numero' => 'T1',
            'capacite' => 4,
            'zone_id' => $this->zone->id,
            'statut' => 'invalide',
        ];

        $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

        $response->assertSessionHasErrors('statut');
        $this->assertDatabaseCount('tables', 0);
    }

    /**
     * Test 15: La creation accepte tous les statuts valides
     */
    public function test_table_creation_accepts_all_valid_statuts(): void
    {
        $validStatuts = ['disponible', 'reservee', 'occupee'];

        foreach ($validStatuts as $index => $statut) {
            $tableData = [
                'numero' => 'T' . ($index + 1),
                'capacite' => 4,
                'zone_id' => $this->zone->id,
                'statut' => $statut,
            ];

            $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

            $response->assertRedirect(route('tables.index'));
            $this->assertDatabaseHas('tables', [
                'numero' => 'T' . ($index + 1),
                'statut' => $statut,
            ]);
        }

        $this->assertDatabaseCount('tables', 3);
    }

    // ========================================
    // TESTS D'UNICITE DU NUMERO PAR ZONE
    // ========================================

    /**
     * Test 16: La creation echoue si le numero existe deja dans la meme zone
     */
    public function test_table_creation_fails_with_duplicate_numero_in_same_zone(): void
    {
        Table::factory()->create([
            'numero' => 'T1',
            'zone_id' => $this->zone->id,
        ]);

        $tableData = [
            'numero' => 'T1',
            'capacite' => 6,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ];

        $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

        $response->assertSessionHasErrors('numero');
        $this->assertDatabaseCount('tables', 1);
    }

    /**
     * Test 17: La creation reussit si le meme numero est dans une zone differente
     */
    public function test_table_creation_succeeds_with_same_numero_in_different_zone(): void
    {
        $zone2 = Zone::factory()->create(['nom' => 'Zone 2']);

        Table::factory()->create([
            'numero' => 'T1',
            'zone_id' => $this->zone->id,
        ]);

        $tableData = [
            'numero' => 'T1',
            'capacite' => 6,
            'zone_id' => $zone2->id,
            'statut' => 'disponible',
        ];

        $response = $this->actingAs($this->admin)->post(route('tables.store'), $tableData);

        $response->assertRedirect(route('tables.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('tables', 2);
    }

    /**
     * Test 18: La modification echoue si le numero existe deja dans la meme zone (autre table)
     */
    public function test_table_update_fails_with_duplicate_numero_in_same_zone(): void
    {
        Table::factory()->create([
            'numero' => 'T1',
            'zone_id' => $this->zone->id,
        ]);

        $table2 = Table::factory()->create([
            'numero' => 'T2',
            'zone_id' => $this->zone->id,
        ]);

        $updateData = [
            'numero' => 'T1',
            'capacite' => 6,
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ];

        $response = $this->actingAs($this->admin)->put(route('tables.update', $table2), $updateData);

        $response->assertSessionHasErrors('numero');
        $this->assertDatabaseHas('tables', ['id' => $table2->id, 'numero' => 'T2']);
    }

    /**
     * Test 19: La modification reussit si on garde le meme numero
     */
    public function test_table_update_succeeds_with_same_numero(): void
    {
        $table = Table::factory()->create([
            'numero' => 'T1',
            'capacite' => 4,
            'zone_id' => $this->zone->id,
        ]);

        $updateData = [
            'numero' => 'T1',
            'capacite' => 8,
            'zone_id' => $this->zone->id,
            'statut' => 'occupee',
        ];

        $response = $this->actingAs($this->admin)->put(route('tables.update', $table), $updateData);

        $response->assertRedirect(route('tables.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'numero' => 'T1',
            'capacite' => 8,
        ]);
    }

    // ========================================
    // TESTS CHANGEMENT DE STATUT RAPIDE
    // ========================================

    /**
     * Test 20: Un admin peut changer le statut d'une table vers disponible
     */
    public function test_admin_can_change_status_to_disponible(): void
    {
        $table = Table::factory()->create([
            'statut' => 'occupee',
            'zone_id' => $this->zone->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('tables.statut', $table), [
            'statut' => 'disponible',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'statut' => 'disponible',
        ]);
    }

    /**
     * Test 21: Un admin peut changer le statut d'une table vers reservee
     */
    public function test_admin_can_change_status_to_reservee(): void
    {
        $table = Table::factory()->create([
            'statut' => 'disponible',
            'zone_id' => $this->zone->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('tables.statut', $table), [
            'statut' => 'reservee',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'statut' => 'reservee',
        ]);
    }

    /**
     * Test 22: Un admin peut changer le statut d'une table vers occupee
     */
    public function test_admin_can_change_status_to_occupee(): void
    {
        $table = Table::factory()->create([
            'statut' => 'disponible',
            'zone_id' => $this->zone->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('tables.statut', $table), [
            'statut' => 'occupee',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'statut' => 'occupee',
        ]);
    }

    /**
     * Test 23: Le changement de statut echoue avec un statut invalide
     */
    public function test_status_change_fails_with_invalid_status(): void
    {
        $table = Table::factory()->create([
            'statut' => 'disponible',
            'zone_id' => $this->zone->id,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('tables.statut', $table), [
            'statut' => 'invalide',
        ]);

        $response->assertSessionHasErrors('statut');
        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'statut' => 'disponible',
        ]);
    }

    // ========================================
    // TESTS DES FILTRES
    // ========================================

    /**
     * Test 24: Filtrage des tables par zone
     */
    public function test_tables_can_be_filtered_by_zone(): void
    {
        $zone2 = Zone::factory()->create(['nom' => 'Zone 2']);

        Table::factory()->create(['numero' => 'T1', 'zone_id' => $this->zone->id]);
        Table::factory()->create(['numero' => 'T2', 'zone_id' => $this->zone->id]);
        Table::factory()->create(['numero' => 'T3', 'zone_id' => $zone2->id]);

        $response = $this->actingAs($this->admin)->get(route('tables.index', ['zone_id' => $this->zone->id]));

        $response->assertStatus(200);
        $tables = $response->viewData('tables');
        $this->assertCount(2, $tables);
        $this->assertTrue($tables->every(fn ($table) => $table->zone_id === $this->zone->id));
    }

    /**
     * Test 25: Filtrage des tables par statut
     */
    public function test_tables_can_be_filtered_by_statut(): void
    {
        Table::factory()->create(['statut' => 'disponible', 'zone_id' => $this->zone->id]);
        Table::factory()->create(['statut' => 'disponible', 'zone_id' => $this->zone->id]);
        Table::factory()->create(['statut' => 'reservee', 'zone_id' => $this->zone->id]);
        Table::factory()->create(['statut' => 'occupee', 'zone_id' => $this->zone->id]);

        $response = $this->actingAs($this->admin)->get(route('tables.index', ['statut' => 'disponible']));

        $response->assertStatus(200);
        $tables = $response->viewData('tables');
        $this->assertCount(2, $tables);
        $this->assertTrue($tables->every(fn ($table) => $table->statut === 'disponible'));
    }

    /**
     * Test 26: Filtrage des tables par zone et statut combines
     */
    public function test_tables_can_be_filtered_by_zone_and_statut(): void
    {
        $zone2 = Zone::factory()->create(['nom' => 'Zone 2']);

        Table::factory()->create(['statut' => 'disponible', 'zone_id' => $this->zone->id]);
        Table::factory()->create(['statut' => 'reservee', 'zone_id' => $this->zone->id]);
        Table::factory()->create(['statut' => 'disponible', 'zone_id' => $zone2->id]);

        $response = $this->actingAs($this->admin)->get(route('tables.index', [
            'zone_id' => $this->zone->id,
            'statut' => 'disponible',
        ]));

        $response->assertStatus(200);
        $tables = $response->viewData('tables');
        $this->assertCount(1, $tables);
        $this->assertEquals('disponible', $tables->first()->statut);
        $this->assertEquals($this->zone->id, $tables->first()->zone_id);
    }

    /**
     * Test 27: Le filtrage avec un statut invalide retourne une erreur de validation
     */
    public function test_filter_with_invalid_statut_returns_validation_error(): void
    {
        $response = $this->actingAs($this->admin)->get(route('tables.index', ['statut' => 'invalide']));

        $response->assertSessionHasErrors('statut');
    }

    /**
     * Test 28: Le filtrage avec une zone inexistante retourne une erreur de validation
     */
    public function test_filter_with_nonexistent_zone_returns_validation_error(): void
    {
        $response = $this->actingAs($this->admin)->get(route('tables.index', ['zone_id' => 9999]));

        $response->assertSessionHasErrors('zone_id');
    }

    // ========================================
    // TESTS DES STATISTIQUES
    // ========================================

    /**
     * Test 29: Les statistiques sont correctement calculees
     */
    public function test_stats_are_correctly_calculated(): void
    {
        Table::factory()->count(3)->create(['statut' => 'disponible', 'zone_id' => $this->zone->id]);
        Table::factory()->count(2)->create(['statut' => 'reservee', 'zone_id' => $this->zone->id]);
        Table::factory()->count(1)->create(['statut' => 'occupee', 'zone_id' => $this->zone->id]);

        $response = $this->actingAs($this->admin)->get(route('tables.index'));

        $response->assertStatus(200);
        $stats = $response->viewData('stats');

        $this->assertEquals(3, $stats['disponible']);
        $this->assertEquals(2, $stats['reservee']);
        $this->assertEquals(1, $stats['occupee']);
    }
}
