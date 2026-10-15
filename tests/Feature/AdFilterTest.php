<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdFilterTest extends TestCase
{
    use RefreshDatabase;

    private Category $cars;
    private Category $phones;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cars = Category::factory()->create();
        $this->phones = Category::factory()->create();

        Ad::factory()->for($this->cars)->create([
            'title' => 'Toyota Corolla 2018',
            'description' => 'Berline climatisée, première main.',
            'price' => 6500000,
            'location' => 'Cocody',
            'condition' => 'used',
        ]);

        Ad::factory()->for($this->phones)->create([
            'title' => 'Samsung Galaxy S23',
            'description' => 'Téléphone neuf sous carton.',
            'price' => 300000,
            'location' => 'Yopougon',
            'condition' => 'new',
        ]);

        Ad::factory()->for($this->phones)->create([
            'title' => 'iPhone 11 reconditionné',
            'description' => 'Batterie remplacée.',
            'price' => 150000,
            'location' => 'Cocody',
            'condition' => 'good',
        ]);
    }

    public function test_home_lists_all_ads_without_filters(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Toyota Corolla 2018')
            ->assertSee('Samsung Galaxy S23')
            ->assertSee('iPhone 11 reconditionné');
    }

    public function test_search_matches_the_title(): void
    {
        $this->get(route('home', ['q' => 'Corolla']))
            ->assertSee('Toyota Corolla 2018')
            ->assertDontSee('Samsung Galaxy S23');
    }

    public function test_search_matches_the_description(): void
    {
        $this->get(route('home', ['q' => 'climatisée']))
            ->assertSee('Toyota Corolla 2018')
            ->assertDontSee('iPhone 11 reconditionné');
    }

    public function test_filter_by_category(): void
    {
        $this->get(route('home', ['categories' => [$this->phones->id]]))
            ->assertSee('Samsung Galaxy S23')
            ->assertSee('iPhone 11 reconditionné')
            ->assertDontSee('Toyota Corolla 2018');
    }

    public function test_filter_by_several_categories(): void
    {
        $this->get(route('home', ['categories' => [$this->cars->id, $this->phones->id]]))
            ->assertSee('Toyota Corolla 2018')
            ->assertSee('Samsung Galaxy S23');
    }

    public function test_filter_by_location(): void
    {
        $this->get(route('home', ['location' => 'Yopougon']))
            ->assertSee('Samsung Galaxy S23')
            ->assertDontSee('Toyota Corolla 2018')
            ->assertDontSee('iPhone 11 reconditionné');
    }

    public function test_filter_by_price_range(): void
    {
        $this->get(route('home', ['price_min' => 100000, 'price_max' => 400000]))
            ->assertSee('Samsung Galaxy S23')
            ->assertSee('iPhone 11 reconditionné')
            ->assertDontSee('Toyota Corolla 2018');
    }

    public function test_price_min_zero_is_a_valid_bound(): void
    {
        $this->get(route('home', ['price_min' => 0, 'price_max' => 200000]))
            ->assertSee('iPhone 11 reconditionné')
            ->assertDontSee('Samsung Galaxy S23');
    }

    public function test_filter_by_condition(): void
    {
        $this->get(route('home', ['condition' => 'new']))
            ->assertSee('Samsung Galaxy S23')
            ->assertDontSee('Toyota Corolla 2018')
            ->assertDontSee('iPhone 11 reconditionné');
    }

    public function test_filters_can_be_combined(): void
    {
        $this->get(route('home', [
            'categories' => [$this->phones->id],
            'location' => 'Cocody',
            'condition' => 'good',
        ]))
            ->assertSee('iPhone 11 reconditionné')
            ->assertDontSee('Samsung Galaxy S23')
            ->assertDontSee('Toyota Corolla 2018');
    }

    public function test_no_result_shows_the_empty_state(): void
    {
        $this->get(route('home', ['q' => 'introuvable-xyz']))
            ->assertOk()
            ->assertSee('Aucune annonce ne correspond');
    }

    public function test_sort_by_price_ascending_and_descending(): void
    {
        $this->get(route('home', ['sort' => 'price_asc']))
            ->assertSeeInOrder(['iPhone 11 reconditionné', 'Samsung Galaxy S23', 'Toyota Corolla 2018']);

        $this->get(route('home', ['sort' => 'price_desc']))
            ->assertSeeInOrder(['Toyota Corolla 2018', 'Samsung Galaxy S23', 'iPhone 11 reconditionné']);
    }

    public function test_invalid_filter_values_are_rejected(): void
    {
        $this->get(route('home', ['condition' => 'cassé']))->assertSessionHasErrors('condition');
        $this->get(route('home', ['location' => 'Paris']))->assertSessionHasErrors('location');
        $this->get(route('home', ['sort' => 'au-hasard']))->assertSessionHasErrors('sort');
        $this->get(route('home', ['price_min' => 'beaucoup']))->assertSessionHasErrors('price_min');
    }

    public function test_results_are_paginated_by_nine(): void
    {
        Ad::factory()->count(10)->create();

        $this->get(route('home'))
            ->assertViewHas('ads', fn ($ads) => $ads->count() === 9 && $ads->total() === 13);

        $this->get(route('home', ['page' => 2]))
            ->assertViewHas('ads', fn ($ads) => $ads->count() === 4);
    }
}
