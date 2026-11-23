<?php

namespace Tests\Unit;

use App\Models\Devise;
use App\Services\CurrencyConverterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test unitaire de la conversion monétaire XOF ↔ EUR / USD / XAF.
 */
class CurrencyConverterServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CurrencyConverterService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed minimal des devises
        Devise::insert([
            ['code' => 'XOF', 'nom' => 'Franc CFA BCEAO', 'symbole' => 'F CFA', 'taux_vers_xof' => 1, 'decimales' => 0, 'is_default' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'XAF', 'nom' => 'Franc CFA BEAC', 'symbole' => 'F CFA', 'taux_vers_xof' => 1, 'decimales' => 0, 'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'EUR', 'nom' => 'Euro', 'symbole' => '€', 'taux_vers_xof' => 655.957, 'decimales' => 2, 'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'USD', 'nom' => 'Dollar US', 'symbole' => '$', 'taux_vers_xof' => 600.00, 'decimales' => 2, 'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->service = new CurrencyConverterService;
    }

    public function test_conversion_xof_vers_eur(): void
    {
        // 655 957 XOF ≈ 1000 EUR
        $result = $this->service->convert(655957, 'XOF', 'EUR');
        $this->assertEqualsWithDelta(1000, $result, 1);
    }

    public function test_conversion_eur_vers_xof(): void
    {
        $result = $this->service->convert(1000, 'EUR', 'XOF');
        $this->assertEqualsWithDelta(655957, $result, 1);
    }

    public function test_conversion_xof_vers_xaf_identique(): void
    {
        $result = $this->service->convert(50000, 'XOF', 'XAF');
        $this->assertEquals(50000, $result);
    }

    public function test_meme_devise_retourne_montant_inchange(): void
    {
        $result = $this->service->convert(123456, 'XOF', 'XOF');
        $this->assertEquals(123456, $result);
    }

    public function test_format_xof_sans_decimales(): void
    {
        $formatted = $this->service->format(1250000, 'XOF');
        $this->assertStringContainsString('1 250 000', $formatted);
        $this->assertStringContainsString('F CFA', $formatted);
    }

    public function test_format_eur_avec_decimales(): void
    {
        $formatted = $this->service->format(1234.5, 'EUR');
        $this->assertStringContainsString('€', $formatted);
    }

    public function test_devise_inconnue_leve_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->convert(100, 'XOF', 'GBP');
    }

    public function test_get_default_currency_est_xof(): void
    {
        $default = $this->service->getDefaultCurrency();
        $this->assertNotNull($default);
        $this->assertEquals('XOF', $default->code);
    }
}
