<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Money\Currency;
use Money\Money;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;


    public function test_it_withdraws_successfully()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
        ]);

        $response = $this->postJson('/api/v1/accounts/123/withdraw', [
            'amount' => 500,
            'currency' => 'BRL',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'account_number' => '123',
                'currency' => 'BRL',
            ]);
        
        $this->assertEquals(500, (int)$response->json('balance')['amount']);
    }

    public function test_it_returns_error_when_amount_is_missing_in_withdraw()
    {
        $response = $this->postJson('/api/v1/accounts/123/withdraw', []);

        $response->assertStatus(422);
    }

    public function test_it_returns_error_when_amount_is_less_than_minimum()
    {
        $response = $this->postJson('/api/v1/accounts/123/withdraw', [
            'amount' => 50,
            'currency' => 'BRL',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_returns_error_when_account_not_found()
    {
        $response = $this->postJson('/api/v1/accounts/999/withdraw', [
            'amount' => 500,
            'currency' => 'BRL',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_returns_error_when_insufficient_funds()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(300, new Currency('BRL')),
        ]);

        $response = $this->postJson('/api/v1/accounts/123/withdraw', [
            'amount' => 500,
            'currency' => 'BRL',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_uses_default_currency_when_not_provided()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
        ]);

        $response = $this->postJson('/api/v1/accounts/123/withdraw', [
            'amount' => 200,
        ]);

        $response->assertStatus(200);

        $this->assertEquals('BRL', $response->json('currency'));
    }

    public function test_it_deposits_successfully()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
        ]);

        $response = $this->postJson('/api/v1/accounts/123/deposit', [
            'amount' => 500,
            'currency' => 'BRL',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'account_number' => '123',
                'currency' => 'BRL',
            ]);

        $this->assertEquals(1500, $response->json('balance'));
    }

    public function test_it_returns_error_when_amount_is_missing_in_deposit()
    {
        $response = $this->postJson('/api/v1/accounts/123/deposit', []);

        $response->assertStatus(422);
    }

    public function test_it_returns_error_when_amount_is_less_than_minimum_in_deposit()
    {
        $response = $this->postJson('/api/v1/accounts/123/deposit', [
            'amount' => 50,
            'currency' => 'BRL',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_returns_error_when_account_not_found_in_deposit()
    {
        $response = $this->postJson('/api/v1/accounts/999/deposit', [
            'amount' => 500,
            'currency' => 'BRL',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_returns_error_when_currency_mismatch()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
        ]);

        $response = $this->postJson('/api/v1/accounts/123/deposit', [
            'amount' => 500,
            'currency' => 'USD',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_uses_default_currency_when_not_provided_in_deposit()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
        ]);

        $response = $this->postJson('/api/v1/accounts/123/deposit', [
            'amount' => 200,
        ]);

        $response->assertStatus(200);

        $this->assertEquals('BRL', $response->json('currency'));
    }

    public function test_it_returns_balance_successfully()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
        ]);

        $response = $this->getJson('/api/v1/accounts/123/balance');

        $response->assertStatus(200)
            ->assertJson([
                'account_number' => '123',
                'balance' => 1000,
                'currency' => 'BRL',
            ]);
    }

    public function test_it_returns_error_when_account_not_found_on_balance()
    {
        $response = $this->getJson('/api/v1/accounts/999/balance');

        $response->assertStatus(422);
    }

    public function test_it_returns_updated_balance_after_deposit_and_withdraw()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
        ]);

        $this->postJson('/api/v1/accounts/123/deposit', [
            'amount' => 500,
            'currency' => 'BRL',
        ]);

        $this->postJson('/api/v1/accounts/123/withdraw', [
            'amount' => 300,
            'currency' => 'BRL',
        ]);

        $response = $this->getJson('/api/v1/accounts/123/balance');

        $response->assertStatus(200)
            ->assertJson([
                'balance' => 1200,
            ]);
    }
}
