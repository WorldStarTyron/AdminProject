<?php

namespace Tests\Feature;

use App\Models\Gebruiker;
use App\Models\Rol;
use App\Models\Lid;
use App\Models\Betaling;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BetalingHistoryTest extends TestCase
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
     * Test that uploading a payment proof for a rejected (niet_betaald) payment
     * updates the existing record instead of creating a duplicate.
     */
    public function test_uploading_proof_for_rejected_payment_reuses_record(): void
    {
        Storage::fake('public');

        // Create the member
        $lid = $this->createLid();
        $gebruiker = $lid->gebruiker;

        // Create a rejected (Openstaand) payment for this member
        $rejectedPayment = Betaling::create([
            'lid_id' => $lid->lid_id,
            'bedrag' => 150.00,
            'status' => 'Openstaand',
            'maand' => 6,
            'jaar' => 2026,
            'methode' => 'overmaking',
            'betaling_bewijs' => 'old_rejected_proof.pdf',
            'ingediend_op' => now()->subDays(2)
        ]);

        // Assert we have exactly 1 payment record initially
        $this->assertEquals(1, Betaling::where('lid_id', $lid->lid_id)->count());

        // Act as the member
        $this->actingAs($gebruiker);

        // Upload a new proof file
        $file = UploadedFile::fake()->create('new_proof.pdf', 100);

        $response = $this->post(route('UploadBewijs'), [
            'betaling_bewijs' => $file
        ]);

        $response->assertStatus(302); // Should redirect back

        // Assert that the total payment count is STILL 1 (no duplicates created)
        $this->assertEquals(1, Betaling::where('lid_id', $lid->lid_id)->count());

        // Assert that the existing record was updated
        $rejectedPayment->refresh();
        $this->assertEquals('in_afwachting', $rejectedPayment->status);
        $this->assertNotEquals('old_rejected_proof.pdf', $rejectedPayment->betaling_bewijs);
        $this->assertNotNull($rejectedPayment->betaling_bewijs);
    }
}
