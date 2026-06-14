<?php

namespace Tests\Feature;

use App\Models\Gebruiker;
use App\Models\Rol;
use App\Models\Lid;
use App\Models\Betaling;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardTest extends TestCase
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
     * Test that the dashboard counts and tables focus on the current month.
     */
    public function test_dashboard_focuses_on_current_month_data(): void
    {
        // 1. Setup administrative user with access to the dashboard
        $admin = $this->createUserWithRole('Applicatie Beheerder');
        $this->actingAs($admin);

        // Capture baseline counts from seeded/existing data
        $baselineResponse = $this->get(route('MainDashboardPagina'));
        $baselineBetaald = $baselineResponse->original->getData()['totaalBetaald'];
        $baselineNietBetaald = $baselineResponse->original->getData()['totaalNietBetaald'];

        // 2. Setup a member with a paid payment for LAST month, and unpaid/outstanding for CURRENT month
        $lid = $this->createLid();

        $lastMonth = now()->subMonth();
        $currentMonth = now();

        // Payment for last month (Paid) — should NOT increase current month's paid count
        Betaling::create([
            'lid_id' => $lid->lid_id,
            'bedrag' => 150.00,
            'status' => 'betaald',
            'maand' => $lastMonth->month,
            'jaar' => $lastMonth->year,
            'methode' => 'overmaking',
            'ingediend_op' => $lastMonth
        ]);

        // Payment for current month (Unpaid/niet_betaald)
        Betaling::create([
            'lid_id' => $lid->lid_id,
            'bedrag' => 150.00,
            'status' => 'niet_betaald',
            'maand' => $currentMonth->month,
            'jaar' => $currentMonth->year,
            'methode' => 'overmaking',
            'ingediend_op' => $currentMonth
        ]);

        // Request dashboard page again after adding test data
        $response = $this->get(route('MainDashboardPagina'));
        $response->assertStatus(200);

        // The last-month paid payment should NOT increase current month's paid count
        $newBetaald = $response->original->getData()['totaalBetaald'];
        $newNietBetaald = $response->original->getData()['totaalNietBetaald'];

        $this->assertEquals(0, $newBetaald - $baselineBetaald, 'Last-month paid payment should not count in current month');
        $this->assertEquals(1, $newNietBetaald - $baselineNietBetaald, 'Current-month unpaid payment should count');

        // Verify the member appears in the dashboard table with correct current-month status
        $dashboardLeden = $response->original->getData()['dashboardLeden'];
        $memberInView = $dashboardLeden->firstWhere('lid_id', $lid->lid_id);

        $this->assertNotNull($memberInView, 'New member should appear in dashboard');

        // Run the current month payment logic used by the view
        $currentMonthPayment = $memberInView->betalingen
            ->where('maand', now()->month)
            ->where('jaar', now()->year)
            ->first();

        $isPaid = $currentMonthPayment && strtolower($currentMonthPayment->status) === 'betaald';

        // Assert that the member is indeed recognized as unpaid for the current month in the table
        $this->assertFalse($isPaid);
    }
}
