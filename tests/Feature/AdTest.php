<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Category $category, array $overrides = []): array
    {
        return array_merge([
            'title' => 'iPhone 13 Pro 256 Go',
            'category_id' => $category->id,
            'description' => 'Très bon état, vendu avec la boîte et le chargeur.',
            'price' => 350000,
            'location' => 'Cocody',
            'condition' => 'good',
            'photos' => [UploadedFile::fake()->image('front.jpg')],
        ], $overrides);
    }

    private function adWithPhoto(User $owner, string $path = 'ads/old.jpg'): Ad
    {
        $ad = Ad::factory()->for($owner)->create();
        $ad->photos()->create(['path' => $path, 'position' => 1]);
        Storage::disk('public')->put($path, 'contenu');

        return $ad;
    }

    // --- Accès ---------------------------------------------------------

    public function test_guests_are_redirected_to_login_to_publish(): void
    {
        $this->get(route('ads.create'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_to_login_for_my_ads(): void
    {
        $this->get(route('ads.mine'))->assertRedirect(route('login'));
    }

    public function test_unverified_users_cannot_publish(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get(route('ads.create'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_anyone_can_see_an_ad(): void
    {
        $ad = Ad::factory()->create(['title' => 'Moto Yamaha Crypton']);

        $this->get(route('ads.show', $ad))
            ->assertOk()
            ->assertSee('Moto Yamaha Crypton');
    }

    // --- Création ------------------------------------------------------

    public function test_verified_user_can_publish_an_ad_with_photos(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('ads.store'), $this->payload($category, [
            'photos' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')],
        ]));

        $ad = Ad::firstOrFail();
        $response->assertRedirect(route('ads.show', $ad));

        $this->assertSame($user->id, $ad->user_id);
        $this->assertSame(350000, $ad->price);
        $this->assertCount(2, $ad->photos);

        foreach ($ad->photos as $photo) {
            Storage::disk('public')->assertExists($photo->path);
        }
    }

    public static function invalidPayloads(): array
    {
        return [
            'titre manquant' => [['title' => ''], 'title'],
            'catégorie inexistante' => [['category_id' => 9999], 'category_id'],
            'description trop courte' => [['description' => 'court'], 'description'],
            'prix négatif' => [['price' => -5], 'price'],
            'prix décimal' => [['price' => 12.5], 'price'],
            'localisation inconnue' => [['location' => 'Paris'], 'location'],
            'état inconnu' => [['condition' => 'cassé'], 'condition'],
            'aucune photo' => [['photos' => []], 'photos'],
        ];
    }

    #[DataProvider('invalidPayloads')]
    public function test_invalid_ads_are_rejected(array $overrides, string $field): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->post(route('ads.store'), $this->payload($category, $overrides))
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('ads', 0);
    }

    public function test_an_ad_cannot_have_more_than_five_photos(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $photos = array_map(fn ($i) => UploadedFile::fake()->image("p{$i}.jpg"), range(1, 6));

        $this->actingAs($user)
            ->post(route('ads.store'), $this->payload($category, ['photos' => $photos]))
            ->assertSessionHasErrors('photos');

        $this->assertDatabaseCount('ads', 0);
    }

    public function test_photos_must_be_images(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->post(route('ads.store'), $this->payload($category, [
                'photos' => [UploadedFile::fake()->create('document.pdf', 50, 'application/pdf')],
            ]))
            ->assertSessionHasErrors('photos.0');
    }

    public function test_photos_cannot_exceed_two_megabytes(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->post(route('ads.store'), $this->payload($category, [
                'photos' => [UploadedFile::fake()->image('lourde.jpg')->size(3000)],
            ]))
            ->assertSessionHasErrors('photos.0');
    }

    // --- Modification --------------------------------------------------

    public function test_owner_can_update_their_ad(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $ad = $this->adWithPhoto($owner);

        $this->actingAs($owner)->put(route('ads.update', $ad), [
            'title' => 'Nouveau titre',
            'category_id' => $ad->category_id,
            'description' => 'Une description suffisamment longue pour passer.',
            'price' => 100000,
            'location' => 'Marcory',
            'condition' => 'new',
        ])->assertRedirect(route('ads.show', $ad));

        $ad->refresh();
        $this->assertSame('Nouveau titre', $ad->title);
        $this->assertSame('Marcory', $ad->location);
        $this->assertCount(1, $ad->photos);
    }

    public function test_owner_can_add_and_remove_photos(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $ad = $this->adWithPhoto($owner, 'ads/old.jpg');
        $oldPhoto = $ad->photos()->first();

        $this->actingAs($owner)->put(route('ads.update', $ad), [
            'title' => $ad->title,
            'category_id' => $ad->category_id,
            'description' => $ad->description,
            'price' => $ad->price,
            'location' => $ad->location,
            'condition' => $ad->condition,
            'remove_photos' => [$oldPhoto->id],
            'photos' => [UploadedFile::fake()->image('new.jpg')],
        ])->assertRedirect(route('ads.show', $ad));

        $ad->refresh();
        $this->assertCount(1, $ad->photos);
        Storage::disk('public')->assertMissing('ads/old.jpg');
        Storage::disk('public')->assertExists($ad->photos->first()->path);
    }

    public function test_an_ad_cannot_lose_its_last_photo(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $ad = $this->adWithPhoto($owner);
        $photo = $ad->photos()->first();

        $this->actingAs($owner)->put(route('ads.update', $ad), [
            'title' => $ad->title,
            'category_id' => $ad->category_id,
            'description' => $ad->description,
            'price' => $ad->price,
            'location' => $ad->location,
            'condition' => $ad->condition,
            'remove_photos' => [$photo->id],
        ])->assertSessionHasErrors('photos');

        $this->assertCount(1, $ad->fresh()->photos);
        Storage::disk('public')->assertExists('ads/old.jpg');
    }

    public function test_other_users_cannot_edit_or_update_an_ad(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $ad = $this->adWithPhoto($owner);

        $this->actingAs($intruder)->get(route('ads.edit', $ad))->assertForbidden();

        $this->actingAs($intruder)->put(route('ads.update', $ad), [
            'title' => 'Piraté',
            'category_id' => $ad->category_id,
            'description' => 'Une description suffisamment longue pour passer.',
            'price' => 1,
            'location' => 'Cocody',
            'condition' => 'used',
        ])->assertForbidden();

        $this->assertNotSame('Piraté', $ad->fresh()->title);
    }

    // --- Suppression ---------------------------------------------------

    public function test_owner_can_delete_their_ad_and_its_photos(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $ad = $this->adWithPhoto($owner);

        $this->actingAs($owner)->delete(route('ads.destroy', $ad))
            ->assertRedirect(route('ads.mine'));

        $this->assertDatabaseMissing('ads', ['id' => $ad->id]);
        $this->assertDatabaseCount('ad_photos', 0);
        Storage::disk('public')->assertMissing('ads/old.jpg');
    }

    public function test_other_users_cannot_delete_an_ad(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $ad = $this->adWithPhoto($owner);

        $this->actingAs($intruder)->delete(route('ads.destroy', $ad))->assertForbidden();

        $this->assertDatabaseHas('ads', ['id' => $ad->id]);
        Storage::disk('public')->assertExists('ads/old.jpg');
    }

    // --- Mes annonces --------------------------------------------------

    public function test_my_ads_page_only_lists_my_own_ads(): void
    {
        $me = User::factory()->create();
        Ad::factory()->for($me)->create(['title' => 'Mon vélo VTT']);
        Ad::factory()->create(['title' => 'Annonce de quelqu un autre']);

        $this->actingAs($me)->get(route('ads.mine'))
            ->assertOk()
            ->assertSee('Mon vélo VTT')
            ->assertDontSee('Annonce de quelqu un autre');
    }
}
