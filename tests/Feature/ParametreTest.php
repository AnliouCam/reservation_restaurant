<?php

namespace Tests\Feature;

use App\Models\Parametre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ParametreTest extends TestCase
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

    // ========================================
    // TESTS DE PROTECTION DES ROUTES (AUTH)
    // ========================================

    /**
     * Test 1: Un utilisateur non authentifie est redirige vers login
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        // Index parametres
        $response = $this->get(route('parametres.index'));
        $response->assertRedirect(route('login'));

        // Update general
        $response = $this->put(route('parametres.update.general'), [
            'nom_restaurant' => 'Test',
            'duree_reservation' => 120,
        ]);
        $response->assertRedirect(route('login'));

        // Horaires
        $response = $this->get(route('parametres.horaires'));
        $response->assertRedirect(route('login'));

        // Update horaires
        $response = $this->put(route('parametres.update.horaires'), []);
        $response->assertRedirect(route('login'));
    }

    // ========================================
    // TESTS D'AUTORISATION (ROLE ADMIN ONLY)
    // ========================================

    /**
     * Test 2: Un utilisateur reception ne peut pas acceder aux parametres (403)
     */
    public function test_reception_user_cannot_access_parametres(): void
    {
        // Index parametres
        $response = $this->actingAs($this->reception)->get(route('parametres.index'));
        $response->assertStatus(403);

        // Update general
        $response = $this->actingAs($this->reception)->put(route('parametres.update.general'), [
            'nom_restaurant' => 'Test',
            'duree_reservation' => 120,
        ]);
        $response->assertStatus(403);

        // Horaires
        $response = $this->actingAs($this->reception)->get(route('parametres.horaires'));
        $response->assertStatus(403);

        // Update horaires
        $response = $this->actingAs($this->reception)->put(route('parametres.update.horaires'), []);
        $response->assertStatus(403);
    }

    // ========================================
    // TESTS PARAMETRES GENERAUX - INDEX
    // ========================================

    /**
     * Test 3: Un admin peut voir la page des parametres
     */
    public function test_admin_can_view_parametres_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('parametres.index'));

        $response->assertStatus(200);
        $response->assertViewIs('parametres.index');
        $response->assertViewHas('parametres');
    }

    /**
     * Test 4: Les parametres par defaut sont initialises automatiquement
     */
    public function test_default_parametres_are_initialized(): void
    {
        $response = $this->actingAs($this->admin)->get(route('parametres.index'));

        $response->assertStatus(200);
        $parametres = $response->viewData('parametres');

        $this->assertEquals('Mon Restaurant', $parametres['nom_restaurant']);
        $this->assertEquals(120, $parametres['duree_reservation']);
    }

    // ========================================
    // TESTS PARAMETRES GENERAUX - UPDATE
    // ========================================

    /**
     * Test 5: Un admin peut sauvegarder les parametres generaux
     */
    public function test_admin_can_update_general_parametres(): void
    {
        $data = [
            'nom_restaurant' => 'Le Gourmet',
            'telephone' => '01 23 45 67 89',
            'adresse' => '123 Rue de la Paix, Paris',
            'email' => 'contact@legourmet.fr',
            'duree_reservation' => 90,
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.general'), $data);

        $response->assertRedirect(route('parametres.index'));
        $response->assertSessionHas('success');

        // Verifier que les valeurs sont bien sauvegardees
        $this->assertEquals('Le Gourmet', Parametre::get('nom_restaurant'));
        $this->assertEquals('01 23 45 67 89', Parametre::get('telephone'));
        $this->assertEquals('123 Rue de la Paix, Paris', Parametre::get('adresse'));
        $this->assertEquals('contact@legourmet.fr', Parametre::get('email'));
        $this->assertEquals(90, Parametre::get('duree_reservation'));
    }

    /**
     * Test 6: La validation echoue si le nom est vide
     */
    public function test_update_fails_with_empty_nom_restaurant(): void
    {
        $data = [
            'nom_restaurant' => '',
            'duree_reservation' => 120,
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.general'), $data);

        $response->assertSessionHasErrors('nom_restaurant');
    }

    /**
     * Test 7: La validation echoue si le nom depasse 100 caracteres
     */
    public function test_update_fails_with_nom_too_long(): void
    {
        $data = [
            'nom_restaurant' => str_repeat('a', 101),
            'duree_reservation' => 120,
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.general'), $data);

        $response->assertSessionHasErrors('nom_restaurant');
    }

    /**
     * Test 8: La validation echoue si l'email est invalide
     */
    public function test_update_fails_with_invalid_email(): void
    {
        $data = [
            'nom_restaurant' => 'Test',
            'email' => 'not-an-email',
            'duree_reservation' => 120,
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.general'), $data);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test 9: La validation echoue si la duree est inferieure a 30
     */
    public function test_update_fails_with_duree_below_minimum(): void
    {
        $data = [
            'nom_restaurant' => 'Test',
            'duree_reservation' => 29,
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.general'), $data);

        $response->assertSessionHasErrors('duree_reservation');
    }

    /**
     * Test 10: La validation echoue si la duree depasse 300
     */
    public function test_update_fails_with_duree_above_maximum(): void
    {
        $data = [
            'nom_restaurant' => 'Test',
            'duree_reservation' => 301,
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.general'), $data);

        $response->assertSessionHasErrors('duree_reservation');
    }

    /**
     * Test 11: Les champs optionnels peuvent etre null
     */
    public function test_optional_fields_can_be_null(): void
    {
        $data = [
            'nom_restaurant' => 'Test Restaurant',
            'telephone' => null,
            'adresse' => null,
            'email' => null,
            'duree_reservation' => 120,
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.general'), $data);

        $response->assertRedirect(route('parametres.index'));
        $response->assertSessionHas('success');
    }

    // ========================================
    // TESTS HORAIRES - INDEX
    // ========================================

    /**
     * Test 12: Un admin peut voir la page des horaires
     */
    public function test_admin_can_view_horaires_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('parametres.horaires'));

        $response->assertStatus(200);
        $response->assertViewIs('parametres.horaires');
        $response->assertViewHas('horaires');
        $response->assertViewHas('jours');
    }

    /**
     * Test 13: Les jours de la semaine sont presents
     */
    public function test_all_days_are_present(): void
    {
        $response = $this->actingAs($this->admin)->get(route('parametres.horaires'));

        $jours = $response->viewData('jours');
        $this->assertEquals(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'], $jours);
    }

    // ========================================
    // TESTS HORAIRES - UPDATE
    // ========================================

    /**
     * Test 14: Un admin peut sauvegarder les horaires
     */
    public function test_admin_can_update_horaires(): void
    {
        $data = [
            'horaires' => [
                'lundi' => ['ouvert' => true, 'midi' => '12:00-14:30', 'soir' => '19:00-22:30'],
                'mardi' => ['ouvert' => true, 'midi' => '12:00-14:30', 'soir' => '19:00-22:30'],
                'mercredi' => ['ouvert' => true, 'midi' => '12:00-14:30', 'soir' => '19:00-22:30'],
                'jeudi' => ['ouvert' => true, 'midi' => '12:00-14:30', 'soir' => '19:00-22:30'],
                'vendredi' => ['ouvert' => true, 'midi' => '12:00-14:30', 'soir' => '19:00-23:00'],
                'samedi' => ['ouvert' => true, 'midi' => '12:00-15:00', 'soir' => '19:00-23:00'],
                'dimanche' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.horaires'), $data);

        $response->assertRedirect(route('parametres.horaires'));
        $response->assertSessionHas('success');

        $horaires = Parametre::get('horaires');
        $this->assertTrue($horaires['lundi']['ouvert']);
        $this->assertEquals('12:00-14:30', $horaires['lundi']['midi']);
        $this->assertEquals('19:00-22:30', $horaires['lundi']['soir']);
        $this->assertFalse($horaires['dimanche']['ouvert']);
    }

    /**
     * Test 15: La validation echoue avec un format d'horaire invalide (midi)
     */
    public function test_update_horaires_fails_with_invalid_midi_format(): void
    {
        $data = [
            'horaires' => [
                'lundi' => ['ouvert' => true, 'midi' => 'invalid', 'soir' => '19:00-22:00'],
                'mardi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'mercredi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'jeudi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'vendredi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'samedi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'dimanche' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.horaires'), $data);

        $response->assertSessionHasErrors('horaires.lundi.midi');
    }

    /**
     * Test 16: La validation echoue avec un format d'horaire invalide (soir)
     */
    public function test_update_horaires_fails_with_invalid_soir_format(): void
    {
        $data = [
            'horaires' => [
                'lundi' => ['ouvert' => true, 'midi' => '12:00-14:00', 'soir' => '19h-22h'],
                'mardi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'mercredi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'jeudi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'vendredi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'samedi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'dimanche' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.horaires'), $data);

        $response->assertSessionHasErrors('horaires.lundi.soir');
    }

    /**
     * Test 17: Le format HH:MM-HH:MM est accepte
     */
    public function test_valid_horaire_format_is_accepted(): void
    {
        $validFormats = ['09:00-12:00', '00:00-23:59', '12:30-14:45'];

        foreach ($validFormats as $format) {
            $data = [
                'horaires' => [
                    'lundi' => ['ouvert' => true, 'midi' => $format, 'soir' => ''],
                    'mardi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                    'mercredi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                    'jeudi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                    'vendredi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                    'samedi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                    'dimanche' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                ],
            ];

            $response = $this->actingAs($this->admin)->put(route('parametres.update.horaires'), $data);

            $response->assertRedirect(route('parametres.horaires'));
            $response->assertSessionHas('success');
        }
    }

    /**
     * Test 18: Les horaires vides sont acceptes (restaurant ferme)
     */
    public function test_empty_horaires_are_accepted(): void
    {
        $data = [
            'horaires' => [
                'lundi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'mardi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'mercredi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'jeudi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'vendredi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'samedi' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
                'dimanche' => ['ouvert' => false, 'midi' => '', 'soir' => ''],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('parametres.update.horaires'), $data);

        $response->assertRedirect(route('parametres.horaires'));
        $response->assertSessionHas('success');
    }

    // ========================================
    // TESTS DU MODELE PARAMETRE
    // ========================================

    /**
     * Test 19: Parametre::get retourne la valeur par defaut si non trouve
     */
    public function test_parametre_get_returns_default_value(): void
    {
        $result = Parametre::get('nonexistent_key', 'default_value');

        $this->assertEquals('default_value', $result);
    }

    /**
     * Test 20: Parametre::set cree ou met a jour un parametre
     */
    public function test_parametre_set_creates_or_updates(): void
    {
        // Create
        Parametre::set('test_key', 'test_value', 'string', 'Test description');
        $this->assertDatabaseHas('parametres', [
            'cle' => 'test_key',
            'valeur' => 'test_value',
            'type' => 'string',
        ]);

        // Update
        Parametre::set('test_key', 'new_value', 'string', 'Test description');
        $this->assertDatabaseHas('parametres', [
            'cle' => 'test_key',
            'valeur' => 'new_value',
        ]);
        $this->assertDatabaseCount('parametres', 1);
    }

    /**
     * Test 21: Le cache est utilise pour les lectures
     */
    public function test_parametre_uses_cache(): void
    {
        Parametre::set('cached_key', 'cached_value', 'string');

        // Premiere lecture (mise en cache)
        $value1 = Parametre::get('cached_key');
        $this->assertEquals('cached_value', $value1);

        // Verifier que le cache existe
        $this->assertTrue(Cache::has('parametre_cached_key'));
    }

    /**
     * Test 22: Le cache est invalide apres set
     */
    public function test_cache_is_invalidated_after_set(): void
    {
        Parametre::set('cache_test', 'old_value', 'string');
        Parametre::get('cache_test'); // Met en cache

        // Modifier la valeur
        Parametre::set('cache_test', 'new_value', 'string');

        // Le cache doit etre invalide
        $this->assertFalse(Cache::has('parametre_cache_test'));
    }

    /**
     * Test 23: castValue fonctionne pour le type json
     */
    public function test_cast_value_json(): void
    {
        $data = ['key' => 'value', 'nested' => ['a' => 1]];
        Parametre::set('json_test', $data, 'json');

        $result = Parametre::get('json_test');

        $this->assertIsArray($result);
        $this->assertEquals('value', $result['key']);
        $this->assertEquals(1, $result['nested']['a']);
    }

    /**
     * Test 24: castValue fonctionne pour le type boolean
     */
    public function test_cast_value_boolean(): void
    {
        Parametre::set('bool_true', '1', 'boolean');
        Parametre::set('bool_false', '0', 'boolean');

        $this->assertTrue(Parametre::get('bool_true'));
        $this->assertFalse(Parametre::get('bool_false'));
    }

    /**
     * Test 25: castValue fonctionne pour le type integer
     */
    public function test_cast_value_integer(): void
    {
        Parametre::set('int_test', '42', 'integer');

        $result = Parametre::get('int_test');

        $this->assertIsInt($result);
        $this->assertEquals(42, $result);
    }

    /**
     * Test 26: initDefaults initialise les parametres par defaut
     */
    public function test_init_defaults_creates_default_parametres(): void
    {
        Parametre::initDefaults();

        $this->assertDatabaseHas('parametres', ['cle' => 'nom_restaurant']);
        $this->assertDatabaseHas('parametres', ['cle' => 'telephone']);
        $this->assertDatabaseHas('parametres', ['cle' => 'adresse']);
        $this->assertDatabaseHas('parametres', ['cle' => 'email']);
        $this->assertDatabaseHas('parametres', ['cle' => 'horaires']);
        $this->assertDatabaseHas('parametres', ['cle' => 'duree_reservation']);
    }

    /**
     * Test 27: initDefaults ne duplique pas les parametres existants
     */
    public function test_init_defaults_does_not_duplicate(): void
    {
        Parametre::set('nom_restaurant', 'Custom Name', 'string');

        Parametre::initDefaults();

        // Doit garder la valeur custom
        $this->assertEquals('Custom Name', Parametre::get('nom_restaurant'));
        // Doit y avoir une seule entree pour nom_restaurant
        $this->assertEquals(1, Parametre::where('cle', 'nom_restaurant')->count());
    }
}
