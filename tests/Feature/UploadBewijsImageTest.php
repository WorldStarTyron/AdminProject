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

class UploadBewijsImageTest extends TestCase
{
    use DatabaseTransactions;

    protected function createLid(): Lid
    {
        $role = Rol::firstOrCreate(['naam' => 'Lid'], ['omschrijving' => 'Test Lid']);

        $user = Gebruiker::create([
            'naam' => 'Test Lid ' . uniqid(),
            'email' => 'lid' . uniqid() . '@example.com',
            'wachtwoord_hash' => bcrypt('password'),
            'status' => 'Actief',
        ]);
        $user->rollen()->attach($role->rol_id);

        return Lid::create([
            'gebruiker_id' => $user->gebruiker_id,
            'lid_type' => 'Actief',
            'telefoonnummer' => '123456',
            'adres' => 'Teststraat 1',
            'woonplaats' => 'Paramaribo',
            'geboortedatum' => '1990-01-01',
        ]);
    }

    public function test_member_can_upload_image_proof(): void
    {
        Storage::fake('public');
        $lid = $this->createLid();
        $this->actingAs($lid->gebruiker);

        $file = UploadedFile::fake()->image('bewijs.jpg');

        $response = $this->post(route('UploadBewijs'), [
            'betaling_bewijs' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);

        $betaling = Betaling::where('lid_id', $lid->lid_id)->first();
        $this->assertNotNull($betaling);
        $this->assertEquals('in_afwachting', $betaling->status);
        $this->assertNotNull($betaling->betaling_bewijs);
    }

    public function test_member_cannot_upload_disallowed_file_type(): void
    {
        Storage::fake('public');
        $lid = $this->createLid();
        $this->actingAs($lid->gebruiker);

        $file = UploadedFile::fake()->create('virus.exe', 50);

        $response = $this->post(route('UploadBewijs'), [
            'betaling_bewijs' => $file,
        ]);

        $response->assertSessionHasErrors('betaling_bewijs');
    }
}
