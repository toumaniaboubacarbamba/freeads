<?php

namespace Tests\Unit;

use App\Models\Ad;
use App\Models\AdPhoto;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModelAccessorsTest extends TestCase
{
    public function test_price_is_formatted_in_fcfa(): void
    {
        $ad = new Ad(['price' => 350000]);

        $this->assertSame("350\u{00A0}000\u{00A0}FCFA", $ad->price_formatted);
    }

    public function test_small_prices_are_formatted_too(): void
    {
        $this->assertSame("500\u{00A0}FCFA", (new Ad(['price' => 500]))->price_formatted);
    }

    public function test_condition_label_is_translated(): void
    {
        $this->assertSame('Neuf', (new Ad(['condition' => 'new']))->condition_label);
        $this->assertSame('Bon état', (new Ad(['condition' => 'good']))->condition_label);
        $this->assertSame('Occasion', (new Ad(['condition' => 'used']))->condition_label);
    }

    public static function phones(): array
    {
        return [
            'format local' => ['0707070707', '2250707070707'],
            'déjà international' => ['+2250707070707', '2250707070707'],
            'sans plus' => ['2250707070707', '2250707070707'],
        ];
    }

    #[DataProvider('phones')]
    public function test_whatsapp_number_is_in_international_format(string $phone, string $expected): void
    {
        $this->assertSame($expected, (new User(['phone' => $phone]))->whatsapp_number);
    }

    public function test_photo_url_points_to_the_public_storage(): void
    {
        $photo = new AdPhoto(['path' => 'ads/abc.jpg']);

        $this->assertStringEndsWith('/storage/ads/abc.jpg', $photo->url);
    }
}
