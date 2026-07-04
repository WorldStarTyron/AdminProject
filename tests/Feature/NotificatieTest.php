<?php

namespace Tests\Feature;

use App\Models\Gebruiker;
use App\Models\Rol;
use App\Models\Lid;
use App\Models\Notificatie;
use App\Models\Betaling;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificatieTest extends TestCase
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
     * TESTCASE 1: Notification Authorization.
     * Iedere ingelogde gebruiker ziet alleen zijn EIGEN notificaties
     * (leden krijgen bv. bericht als hun betaling is goedgekeurd of afgekeurd).
     */
    public function test_notification_endpoints_require_login(): void
    {
        // 1. Unauthenticated users cannot access notifications
        $response = $this->getJson(route('notificaties.index'));
        $response->assertStatus(401);

        // 2. Leden mogen hun eigen notificaties bekijken en op gelezen zetten
        $lid = $this->createLid();
        $this->actingAs($lid->gebruiker);

        $response = $this->getJson(route('notificaties.index'));
        $response->assertStatus(200);

        $response = $this->postJson(route('notificaties.lezen'));
        $response->assertStatus(200);

        // 3. Admin roles can successfully access the endpoints (200)
        $admin = $this->createUserWithRole('Administratie Medewerker');
        $this->actingAs($admin);

        $response = $this->getJson(route('notificaties.index'));
        $response->assertStatus(200);

        $response = $this->postJson(route('notificaties.lezen'));
        $response->assertStatus(200);
    }

    /**
     * TESTCASE 2: Notification Generation upon Payment Registration.
     * Verifies that registering a new payment triggers automated notifications
     * for all administrative users (Administratie Medewerker, Applicatie Beheerder) with the correct type.
     */
    public function test_registering_a_payment_creates_notifications_for_admins(): void
    {
        // Setup administrative users
        $medewerker = $this->createUserWithRole('Administratie Medewerker');
        $beheerder = $this->createUserWithRole('Applicatie Beheerder');
        
        // Setup a member for whom payment is registered
        $lid = $this->createLid();
        $gebruiker = $lid->gebruiker;

        // Perform payment registration as the medewerker
        $this->actingAs($medewerker);

        $response = $this->postJson(route('betalingPagina.addBetaling.store'), [
            'naam' => $gebruiker->naam,
            'datum' => '2026-06-12',
            'methode' => 'overmaking',
            'status' => 'betaald',
            'bedrag' => 150.00
        ]);

        $response->assertStatus(201);

        // Verify that notifications are created in the database for both administrators
        $this->assertDatabaseHas('notificatie', [
            'gebruiker_id' => $medewerker->gebruiker_id,
            'lid_id' => $lid->lid_id,
            'Notif_type' => 'Betaling_ingediend',
            'gelezen' => 0
        ]);

        $this->assertDatabaseHas('notificatie', [
            'gebruiker_id' => $beheerder->gebruiker_id,
            'lid_id' => $lid->lid_id,
            'Notif_type' => 'Betaling_ingediend',
            'gelezen' => 0
        ]);
    }

    /**
     * TESTCASE 3: Marking Notifications as Read.
     * Verifies that sending a POST request to markeerGelezen sets all unread notifications
     * of the authenticated admin user to gelezen = 1 (true).
     */
    public function test_marking_notifications_as_read(): void
    {
        $admin = $this->createUserWithRole('Administratie Medewerker');
        $lid = $this->createLid();

        // Create an unread notification for the admin
        $notif = Notificatie::create([
            'gebruiker_id' => $admin->gebruiker_id,
            'lid_id' => $lid->lid_id,
            'Notif_type' => 'Betaling_ingediend',
            'titel' => 'Test notificatie',
            'gelezen' => false,
            'gestuurd_op' => now()
        ]);

        // Act as admin and call mark-as-read
        $this->actingAs($admin);

        $response = $this->postJson(route('notificaties.lezen'));
        $response->assertStatus(200);

        // Check that the database shows it has been updated to gelezen = 1
        $this->assertDatabaseHas('notificatie', [
            'notificatie_id' => $notif->notificatie_id,
            'gelezen' => 1
        ]);
    }

    /**
     * TESTCASE 4: Member uploading payment proof generates notifications for admins.
     * Verifies that when a member uploads a payment proof file,
     * notifications with the correct title and type are created in the database for all administrative users.
     */
    public function test_member_uploading_payment_proof_creates_notifications_for_admins(): void
    {
        Storage::fake('public');

        // Setup administrative users
        $medewerker = $this->createUserWithRole('Administratie Medewerker');
        $beheerder = $this->createUserWithRole('Applicatie Beheerder');

        // Setup a member who will upload the proof
        $lid = $this->createLid();
        $gebruiker = $lid->gebruiker;

        // Act as the member
        $this->actingAs($gebruiker);

        // Upload payment proof
        $file = UploadedFile::fake()->create('proof.pdf', 100);

        $response = $this->post(route('UploadBewijs'), [
            'betaling_bewijs' => $file
        ]);

        $response->assertStatus(302); // Expect redirect back with success message

        // Verify that notifications are created in the database for both administrators
        $this->assertDatabaseHas('notificatie', [
            'gebruiker_id' => $medewerker->gebruiker_id,
            'lid_id' => $lid->lid_id,
            'Notif_type' => 'Betaling_ingediend',
            'titel' => "Lid {$gebruiker->naam} heeft een betalingsbewijs geüpload.",
            'gelezen' => 0
        ]);

        $this->assertDatabaseHas('notificatie', [
            'gebruiker_id' => $beheerder->gebruiker_id,
            'lid_id' => $lid->lid_id,
            'Notif_type' => 'Betaling_ingediend',
            'titel' => "Lid {$gebruiker->naam} heeft een betalingsbewijs geüpload.",
            'gelezen' => 0
        ]);
    }
}
