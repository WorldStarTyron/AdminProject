<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gebruiker;
use App\Models\Activiteit;
use App\Models\Rol;
use App\Models\Lid;
use App\Models\Betaling;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ActiviteitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $adminRol = Rol::firstOrCreate(
            ['naam' => 'Admin'],
            ['omschrijving' => 'Administrator toegang met volledige bevoegdheden']
        );
        $lidRol = Rol::firstOrCreate(
            ['naam' => 'Lid'],
            ['omschrijving' => 'Standaard lidmaatschap toegang']
        );

        // Ensure user 'Regeffio' exists to perform administrative tasks
        $regeffio = Gebruiker::firstOrCreate(
            ['email' => 'regeffio@admin.com'],
            [
                'naam'            => 'Regeffio',
                'wachtwoord_hash' => Hash::make('Welkom123'),
                'status'          => 'Actief',
            ]
        );
        if (!$regeffio->rollen->contains('rol_id', $adminRol->rol_id)) {
            $regeffio->rollen()->attach($adminRol->rol_id);
        }

        // Ensure user 'Ali' exists to perform user tasks
        $ali = Gebruiker::firstOrCreate(
            ['email' => 'ali@user.com'],
            [
                'naam'            => 'Ali',
                'wachtwoord_hash' => Hash::make('Welkom123'),
                'status'          => 'Actief',
            ]
        );
        if (!$ali->rollen->contains('rol_id', $lidRol->rol_id)) {
            $ali->rollen()->attach($lidRol->rol_id);
        }

        // Truncate existing logs
        DB::table('activiteit')->truncate();

        $nu = Carbon::now();

        // Exact activity logs requested by the user
        $logs = [
            [
                'actor'    => $regeffio->gebruiker_id,
                'actie'    => 'ingelogd',
                'date'     => $nu->copy()->subDays(6)->setHour(9)->setMinute(15),
                'details'  => [
                    'ip'         => '192.168.1.55',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'details'    => 'Gebruiker Regeffio heeft ingelogd.'
                ]
            ],
            [
                'actor'    => $regeffio->gebruiker_id,
                'actie'    => 'lid_aangemaakt',
                'date'     => $nu->copy()->subDays(5)->setHour(10)->setMinute(30),
                'details'  => [
                    'lid_naam'  => 'Ahmed Khan',
                    'lid_email' => 'ahmed.khan@gmail.com',
                    'details'   => 'Gebruiker Regeffio heeft een nieuw lid toegevoegd: Ahmed Khan.'
                ]
            ],
            [
                'actor'    => $regeffio->gebruiker_id,
                'actie'    => 'lid_bijgewerkt',
                'date'     => $nu->copy()->subDays(4)->setHour(14)->setMinute(22),
                'details'  => [
                    'lid_naam'  => 'Ahmed Khan',
                    'wijziging' => 'woonplaats naar Paramaribo',
                    'details'   => 'Gebruiker Regeffio heeft de gegevens van Ahmed Khan bijgewerkt.'
                ]
            ],
            [
                'actor'    => $regeffio->gebruiker_id,
                'actie'    => 'betaling_geregistreerd',
                'date'     => $nu->copy()->subDays(3)->setHour(11)->setMinute(45),
                'details'  => [
                    'lid_naam' => 'Ahmed Khan',
                    'bedrag'   => 500,
                    'methode'  => 'overmaking',
                    'details'  => 'Gebruiker Regeffio heeft een betaling van SRD 500 geregistreerd voor Ahmed Khan.'
                ]
            ],
            [
                'actor'    => $regeffio->gebruiker_id,
                'actie'    => 'lid_aangemaakt', // mapped with sub-string check to "Gebruiker aangemaakt"
                'date'     => $nu->copy()->subDays(2)->setHour(15)->setMinute(10),
                'details'  => [
                    'gebruiker' => 'Ali',
                    'rol'       => 'Lid',
                    'details'   => 'Gebruiker Regeffio heeft een nieuwe gebruiker toegevoegd: Ali.'
                ]
            ],
            [
                'actor'    => $ali->gebruiker_id,
                'actie'    => 'wachtwoord_gewijzigd',
                'date'     => $nu->copy()->subDays(2)->setHour(15)->setMinute(30),
                'details'  => [
                    'method'  => 'verificatie_code',
                    'details' => 'Gebruiker Ali heeft zijn wachtwoord gewijzigd.'
                ]
            ],
            [
                'actor'    => $regeffio->gebruiker_id,
                'actie'    => 'bon_gedownload', // maps to "Rapport gegenereerd"
                'date'     => $nu->copy()->subDays(1)->setHour(16)->setMinute(20),
                'details'  => [
                    'periode' => '01-05-2026 t/m 31-05-2026',
                    'details' => 'Gebruiker Regeffio heeft een betalingsrapport gegenereerd voor periode 01-05-2026 t/m 31-05-2026.'
                ]
            ],
            [
                'actor'    => $regeffio->gebruiker_id,
                'actie'    => 'lid_verwijderd',
                'date'     => $nu->copy()->subHours(4)->setMinute(15),
                'details'  => [
                    'lid_naam' => 'Ahmed Khan',
                    'details'  => 'Gebruiker Regeffio heeft lid Ahmed Khan verwijderd.'
                ]
            ],
            [
                'actor'    => $regeffio->gebruiker_id,
                'actie'    => 'uitgelogd',
                'date'     => $nu->copy()->subHours(2)->setMinute(45),
                'details'  => [
                    'ip'      => '192.168.1.55',
                    'details' => 'Gebruiker Regeffio heeft uitgelogd.'
                ]
            ]
        ];

        // Seed logs in order of their dates
        foreach ($logs as $log) {
            Activiteit::create([
                'gebruiker_id'  => $log['actor'],
                'actie'         => $log['actie'],
                'details'       => $log['details'],
                'aangemaakt_op' => $log['date'],
            ]);
        }

        $this->command->info('Succesvol ' . count($logs) . ' aangepaste activiteit logs gegenereerd volgens de gevraagde specificaties!');
    }
}
