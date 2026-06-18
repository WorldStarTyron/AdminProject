<?php

namespace Tests\Feature;

use App\Models\Gebruiker;
use App\Models\Rol;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LedenpaginaAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected function createUserWithRole(string $roleName): Gebruiker
    {
        $role = Rol::firstOrCreate(
            ['naam' => $roleName],
            ['omschrijving' => "Test role {$roleName}"]
        );

        $user = Gebruiker::create([
            'naam' => "Test {$roleName} " . uniqid(),
            'email' => strtolower(str_replace(' ', '', $roleName)) . uniqid() . '@example.com',
            'wachtwoord_hash' => bcrypt('password'),
            'status' => 'Actief',
        ]);

        $user->rollen()->attach($role->rol_id);

        return $user;
    }

    public function test_voorzitter_can_access_ledenpagina(): void
    {
        $voorzitter = $this->createUserWithRole('Voorzitter');
        $this->actingAs($voorzitter);

        $this->get(route('ledenpagina'))->assertOk();
    }

    public function test_administratie_medewerker_can_access_ledenpagina(): void
    {
        $medewerker = $this->createUserWithRole('Administratie Medewerker');
        $this->actingAs($medewerker);

        $this->get(route('ledenpagina'))->assertOk();
    }

    public function test_applicatie_beheerder_can_access_ledenpagina(): void
    {
        $beheerder = $this->createUserWithRole('Applicatie Beheerder');
        $this->actingAs($beheerder);

        $this->get(route('ledenpagina'))->assertOk();
    }

    public function test_lid_role_cannot_access_ledenpagina(): void
    {
        $lid = $this->createUserWithRole('Lid');
        $this->actingAs($lid);

        $this->get(route('ledenpagina'))->assertForbidden();
    }

    public function test_direct_url_access_requires_leden_bekijken_gate(): void
    {
        $voorzitter = $this->createUserWithRole('Voorzitter');
        $this->actingAs($voorzitter);

        $this->get('/ledenpagina')->assertOk();
    }

    public function test_sidebar_shows_leden_link_for_authorized_roles(): void
    {
        $voorzitter = $this->createUserWithRole('Voorzitter');
        $this->actingAs($voorzitter);

        $response = $this->get(route('MainDashboardPagina'));
        $response->assertOk();
        $response->assertSee('data-tooltip="Leden"', false);
    }
}
