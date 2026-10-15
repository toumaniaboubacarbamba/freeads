<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoDiskTest extends TestCase
{
    use RefreshDatabase;

    public function test_photos_are_stored_on_the_configured_disk(): void
    {
        // En production, PHOTO_DISK=s3 envoie les photos sur le stockage externe.
        config(['akwaba.photo_disk' => 'cloud']);
        Storage::fake('cloud');
        Storage::fake('public');

        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)->post(route('ads.store'), [
            'title' => 'Moto Yamaha Crypton',
            'category_id' => $category->id,
            'description' => 'Moto en très bon état, papiers à jour.',
            'price' => 380000,
            'location' => 'Abobo',
            'condition' => 'used',
            'photos' => [UploadedFile::fake()->image('moto.jpg')],
        ])->assertSessionHasNoErrors();

        $path = Ad::firstOrFail()->photos->first()->path;

        Storage::disk('cloud')->assertExists($path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_deleting_an_ad_removes_its_photos_from_the_configured_disk(): void
    {
        config(['akwaba.photo_disk' => 'cloud']);
        Storage::fake('cloud');

        $user = User::factory()->create();
        $ad = Ad::factory()->for($user)->create();
        $ad->photos()->create(['path' => 'ads/x.jpg', 'position' => 1]);
        Storage::disk('cloud')->put('ads/x.jpg', 'contenu');

        $this->actingAs($user)->delete(route('ads.destroy', $ad));

        Storage::disk('cloud')->assertMissing('ads/x.jpg');
    }
}
