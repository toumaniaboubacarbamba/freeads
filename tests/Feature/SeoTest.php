<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    // --- Accueil -------------------------------------------------------

    public function test_home_has_title_description_and_canonical(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Petites annonces gratuites en Côte d', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
            ->assertSee('<meta property="og:image"', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertDontSee('name="robots"', false);
    }

    public function test_filtered_home_is_not_indexed(): void
    {
        $this->get(route('home', ['q' => 'iphone']))
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false);
    }

    public function test_default_sort_alone_does_not_make_the_page_noindex(): void
    {
        $this->get(route('home', ['sort' => 'latest']))
            ->assertDontSee('name="robots"', false);
    }

    public function test_second_page_has_its_own_canonical(): void
    {
        Ad::factory()->count(10)->create();

        $this->get(route('home', ['page' => 2]))
            ->assertSee('<link rel="canonical" href="'.route('home', ['page' => 2]).'">', false);
    }

    // --- Page d'une annonce -------------------------------------------

    public function test_ad_page_has_social_tags_and_structured_data(): void
    {
        $ad = Ad::factory()->create([
            'title' => 'iPhone 13 Pro',
            'price' => 350000,
            'location' => 'Cocody',
            'condition' => 'good',
        ]);
        $ad->photos()->create(['path' => 'ads/front.jpg', 'position' => 1]);

        $this->get(route('ads.show', $ad))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('ads.show', $ad).'">', false)
            ->assertSee('<meta property="og:type" content="product">', false)
            ->assertSee('<meta property="og:image" content="'.asset('storage/ads/front.jpg').'">', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"Product"', false)
            ->assertSee('"price":"350000"', false)
            ->assertSee('"priceCurrency":"XOF"', false)
            ->assertSee('https://schema.org/UsedCondition', false);
    }

    public function test_structured_data_cannot_be_used_to_inject_a_script(): void
    {
        $ad = Ad::factory()->create(['title' => '</script><script>alert(1)</script>']);

        $this->get(route('ads.show', $ad))
            ->assertOk()
            ->assertDontSee('</script><script>alert(1)</script>', false);
    }

    // --- URLs lisibles -------------------------------------------------

    public function test_ad_urls_contain_a_readable_slug(): void
    {
        $ad = Ad::factory()->create(['title' => 'Toyota Corolla 2018 Climatisée']);

        $this->assertStringEndsWith(
            '/ads/'.$ad->id.'-toyota-corolla-2018-climatisee',
            route('ads.show', $ad)
        );
    }

    public function test_ad_resolves_with_slug_without_slug_or_with_an_old_slug(): void
    {
        $ad = Ad::factory()->create(['title' => 'Vélo VTT']);

        $this->get(route('ads.show', $ad))->assertOk();
        $this->get('/ads/'.$ad->id)->assertOk();
        $this->get('/ads/'.$ad->id.'-ancien-titre')->assertOk();
    }

    public function test_unknown_ad_returns_404(): void
    {
        $this->get('/ads/999999-introuvable')->assertNotFound();
    }

    // --- robots.txt et sitemap ----------------------------------------

    public function test_robots_txt_blocks_private_pages_and_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /profile', false)
            ->assertSee('Disallow: /mes-annonces', false)
            ->assertSee('Sitemap: '.route('sitemap'), false);
    }

    public function test_sitemap_lists_the_home_and_every_ad(): void
    {
        $ad = Ad::factory()->create();

        $response = $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('home'), false)
            ->assertSee(route('ads.show', $ad), false);

        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('<?xml', $response->getContent());
    }

    // --- Pages privées -------------------------------------------------

    public function test_private_pages_are_noindex(): void
    {
        $user = User::factory()->create();

        foreach ([route('ads.mine'), route('ads.create'), route('profile.edit')] as $url) {
            $this->actingAs($user)->get($url)
                ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        }
    }

    public function test_login_and_register_pages_are_noindex(): void
    {
        $this->get('/login')->assertSee('noindex', false);
        $this->get('/register')->assertSee('noindex', false);
    }
}
