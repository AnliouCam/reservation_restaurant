<?php

namespace Tests\Feature;

use App\Models\Table;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZoneTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $reception;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->reception = User::factory()->reception()->create();
    }

    /**
     * Test 1: Un admin peut voir la liste des zones
     */
    public function test_admin_can_view_zones_list(): void
    {
        Zone::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('zones.index'));

        $response->assertStatus(200);
        $response->assertViewIs('zones.index');
        $response->assertViewHas('zones');
    }

    /**
     * Test 2: Un admin peut creer une zone valide
     */
    public function test_admin_can_create_valid_zone(): void
    {
        $zoneData = [
            'nom' => 'Terrasse',
            'description' => 'Zone exterieure avec vue',
        ];

        $response = $this->actingAs($this->admin)->post(route('zones.store'), $zoneData);

        $response->assertRedirect(route('zones.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('zones', ['nom' => 'Terrasse']);
    }

    /**
     * Test 3: La creation echoue si le nom est vide
     */
    public function test_zone_creation_fails_with_empty_name(): void
    {
        $zoneData = [
            'nom' => '',
            'description' => 'Une description',
        ];

        $response = $this->actingAs($this->admin)->post(route('zones.store'), $zoneData);

        $response->assertSessionHasErrors('nom');
        $this->assertDatabaseMissing('zones', ['description' => 'Une description']);
    }

    /**
     * Test 4: La creation echoue si le nom existe deja
     */
    public function test_zone_creation_fails_with_duplicate_name(): void
    {
        Zone::factory()->create(['nom' => 'Terrasse']);

        $zoneData = [
            'nom' => 'Terrasse',
            'description' => 'Autre description',
        ];

        $response = $this->actingAs($this->admin)->post(route('zones.store'), $zoneData);

        $response->assertSessionHasErrors('nom');
        $this->assertDatabaseCount('zones', 1);
    }

    /**
     * Test 5: Un admin peut modifier une zone
     */
    public function test_admin_can_update_zone(): void
    {
        $zone = Zone::factory()->create(['nom' => 'Ancienne Zone']);

        $updateData = [
            'nom' => 'Nouvelle Zone',
            'description' => 'Description mise a jour',
        ];

        $response = $this->actingAs($this->admin)->put(route('zones.update', $zone), $updateData);

        $response->assertRedirect(route('zones.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('zones', ['nom' => 'Nouvelle Zone']);
        $this->assertDatabaseMissing('zones', ['nom' => 'Ancienne Zone']);
    }

    /**
     * Test 6: Un admin peut supprimer une zone sans tables
     */
    public function test_admin_can_delete_zone_without_tables(): void
    {
        $zone = Zone::factory()->create(['nom' => 'Zone a supprimer']);

        $response = $this->actingAs($this->admin)->delete(route('zones.destroy', $zone));

        $response->assertRedirect(route('zones.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('zones', ['nom' => 'Zone a supprimer']);
    }

    /**
     * Test 7: La suppression echoue si la zone contient des tables
     */
    public function test_zone_deletion_fails_when_zone_has_tables(): void
    {
        $zone = Zone::factory()->create();

        // Create a table in this zone
        Table::factory()->create(['zone_id' => $zone->id]);

        $response = $this->actingAs($this->admin)->delete(route('zones.destroy', $zone));

        $response->assertRedirect(route('zones.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('zones', ['id' => $zone->id]);
    }

    /**
     * Test 8: Un utilisateur reception ne peut pas acceder aux zones (403)
     */
    public function test_reception_user_cannot_access_zones(): void
    {
        $response = $this->actingAs($this->reception)->get(route('zones.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->reception)->get(route('zones.create'));
        $response->assertStatus(403);

        $zone = Zone::factory()->create();

        $response = $this->actingAs($this->reception)->post(route('zones.store'), [
            'nom' => 'Test',
            'description' => 'Test',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($this->reception)->get(route('zones.edit', $zone));
        $response->assertStatus(403);

        $response = $this->actingAs($this->reception)->put(route('zones.update', $zone), [
            'nom' => 'Test Update',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($this->reception)->delete(route('zones.destroy', $zone));
        $response->assertStatus(403);
    }

    /**
     * Test 9: Un utilisateur non authentifie est redirige vers login
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('zones.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('zones.create'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('zones.store'), [
            'nom' => 'Test',
        ]);
        $response->assertRedirect(route('login'));

        $zone = Zone::factory()->create();

        $response = $this->get(route('zones.edit', $zone));
        $response->assertRedirect(route('login'));

        $response = $this->put(route('zones.update', $zone), [
            'nom' => 'Test',
        ]);
        $response->assertRedirect(route('login'));

        $response = $this->delete(route('zones.destroy', $zone));
        $response->assertRedirect(route('login'));
    }
}
