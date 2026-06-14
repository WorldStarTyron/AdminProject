<?php

namespace Tests\Feature;

use App\Models\Gebruiker;
use App\Models\Rol;
use App\Models\Lid;
use App\Models\Activiteit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GebruikerBeheerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Helper to create a user with a specific role.
     */
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
            'status' => 'Actief'
        ]);

        $user->rollen()->attach($role->rol_id);

        return $user;
    }

    /**
     * Helper to create a member (Lid).
     */
    protected function createLid(): Lid
    {
        $user = $this->createUserWithRole('Lid');

        return Lid::create([
            'gebruiker_id' => $user->gebruiker_id,
            'lid_type' => 'Actief',
            'telefoonnummer' => '123456',
            'adres' => 'Teststraat 1',
            'woonplaats' => 'Paramaribo',
            'geboortedatum' => '1990-01-01'
        ]);
    }

    /**
     * Test authorized access to the User Management page.
     */
    public function test_gebruikersbeheer_page_access(): void
    {
        // 1. Unauthenticated redirect to login
        $response = $this->get(route('GebruikersBeheer'));
        $response->assertRedirect(route('login'));

        // 2. Members are forbidden (403)
        $lid = $this->createLid();
        $this->actingAs($lid->gebruiker);
        $response = $this->get(route('GebruikersBeheer'));
        $response->assertStatus(403);

        // 3. Admin / Beheerder has access
        $admin = $this->createUserWithRole('Applicatie Beheerder');
        $this->actingAs($admin);
        $response = $this->get(route('GebruikersBeheer'));
        $response->assertStatus(200);
    }

    /**
     * Test updating a user's details and asserting activity is logged.
     */
    public function test_updating_user_details(): void
    {
        $admin = $this->createUserWithRole('Applicatie Beheerder');
        $targetUser = $this->createUserWithRole('Lid');

        $this->actingAs($admin);

        $response = $this->put(route('GebruikersBeheer.update', $targetUser->gebruiker_id), [
            'naam' => 'Nieuwe Naam',
            'email' => 'nieuw_email@example.com',
            'status' => 'Inactief',
        ]);

        $response->assertRedirect(route('GebruikersBeheer'));
        $response->assertSessionHas('success');

        // Check database changes
        $this->assertDatabaseHas('gebruikers', [
            'gebruiker_id' => $targetUser->gebruiker_id,
            'naam' => 'Nieuwe Naam',
            'email' => 'nieuw_email@example.com',
            'status' => 'Inactief',
        ]);

        // Check activity log
        $this->assertDatabaseHas('activiteit', [
            'gebruiker_id' => $admin->gebruiker_id,
            'actie' => 'gebruiker_gewijzigd',
        ]);
    }

    /**
     * Test deleting a user.
     */
    public function test_deleting_user(): void
    {
        $admin = $this->createUserWithRole('Applicatie Beheerder');
        
        // Create a user who is also a Lid
        $lid = $this->createLid();
        $targetUser = $lid->gebruiker;

        $this->actingAs($admin);

        // Deleting another user should succeed
        $response = $this->delete(route('GebruikersBeheer.destroy', $targetUser->gebruiker_id));
        $response->assertRedirect(route('GebruikersBeheer'));

        // Check database: user, lid and rollen associations should be gone
        $this->assertDatabaseMissing('gebruikers', ['gebruiker_id' => $targetUser->gebruiker_id]);
        $this->assertDatabaseMissing('leden', ['gebruiker_id' => $targetUser->gebruiker_id]);
        $this->assertDatabaseMissing('gebruikers_rollen', ['gebruiker_id' => $targetUser->gebruiker_id]);

        // Assert activity log is generated
        $this->assertDatabaseHas('activiteit', [
            'gebruiker_id' => $admin->gebruiker_id,
            'actie' => 'gebruiker_verwijderd',
        ]);

        // Try to delete self: should fail
        $responseSelf = $this->delete(route('GebruikersBeheer.destroy', $admin->gebruiker_id));
        $responseSelf->assertSessionHas('error');
        $this->assertDatabaseHas('gebruikers', ['gebruiker_id' => $admin->gebruiker_id]);
    }
}
