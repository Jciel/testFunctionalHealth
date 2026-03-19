<?php

namespace Tests\Unit;

use App\Http\Services\AccountService;
use App\Models\Account;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Money\Currency;
use Money\Money;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    protected AccountService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AccountService::class);
    }

    public function test_it_withdraws_successfully()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
            'currency' => 'BRL',
        ]);

        $amount = new Money(500, new Currency('BRL'));

        $result = $this->service->withdraw('123', $amount);

        $this->assertEquals('123', $result['account_number']);
        $this->assertEquals(500, $result['balance']->getAmount()); // 1000 - 500
    }

    public function test_it_throws_exception_when_account_not_found_on_withdraw()
    {
        $this->expectException(ValidationException::class);

        $amount = new Money(500, new Currency('BRL'));

        $this->service->withdraw('999', $amount);
    }

    public function test_it_throws_exception_when_insufficient_funds()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(300, new Currency('BRL')),
            'currency' => 'BRL',
        ]);

        $amount = new Money(500, new Currency('BRL'));

        $this->expectException(ValidationException::class);

        $this->service->withdraw('123', $amount);
    }

    public function test_it_throws_exception_when_amount_is_zero()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
            'currency' => 'BRL',
        ]);

        $this->expectException(ValidationException::class);

        $amount = new Money(0, new Currency('BRL'));

        $this->service->withdraw('123', $amount);
    }

    public function test_it_throws_exception_when_amount_is_negative()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
            'currency' => 'BRL',
        ]);

        $this->expectException(ValidationException::class);

        $amount = new Money(-100, new Currency('BRL'));

        $this->service->withdraw('123', $amount);
    }

    public function test_it_deposits_amount_successfully()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
            'currency' => 'BRL',
        ]);

        $amount = new Money(500, new Currency('BRL'));

        $result = $this->service->deposit('123', $amount);

        $this->assertEquals('123', $result['account_number']);
        $this->assertEquals(1500, $result['balance']);
        $this->assertEquals('BRL', $result['currency']);

        $this->assertDatabaseHas('accounts', ['number' => '123']);

        $this->assertDatabaseHas('operations', [
            'type' => 'deposit',
            'account_id' => $account->id,
        ]);
    }

    public function test_it_throws_exception_when_account_not_found_on_deposit()
    {
        $this->expectException(ValidationException::class);

        $amount = new Money(500, new Currency('BRL'));

        $this->service->deposit('999', $amount);
    }

    public function test_it_throws_exception_when_deposit_amount_is_zero()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
            'currency' => 'BRL',
        ]);

        $this->expectException(ValidationException::class);

        $amount = new Money(0, new Currency('BRL'));

        $this->service->deposit('123', $amount);
    }

    public function it_throws_exception_when_deposit_amount_is_negative()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
            'currency' => 'BRL',
        ]);

        $this->expectException(ValidationException::class);

        $amount = new Money(-500, new Currency('BRL'));

        $this->service->deposit('123', $amount);
    }

    public function it_throws_exception_when_currency_mismatch()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => new Money(1000, new Currency('BRL')),
            'currency' => 'BRL',
        ]);

        $this->expectException(ValidationException::class);

        $amount = new Money(500, new Currency('USD'));

        $this->service->deposit('123', $amount);
    }

    public function test_it_returns_balance_when_account_exists()
    {
        $account = Account::factory()->create([
            'number' => '123',
            'balance' => Money::BRL(1000),
            'currency' => 'BRL',
        ]);

        $result = $this->service->balance('123');

        $this->assertEquals('123', $result['account_number']);
        $this->assertEquals(1000, $result['balance']);
        $this->assertEquals('BRL', $result['currency']);
    }

    public function test_it_throws_exception_when_account_not_found()
    {
        $this->expectException(ValidationException::class);
        $this->service->balance('999');
    }
}
