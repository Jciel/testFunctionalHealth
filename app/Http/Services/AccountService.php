<?php

namespace App\Http\Services;

use App\Models\Account;
use App\Models\Operation;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Money\Money;

class AccountService
{
    public function __construct(private Account $account)
    {
    }

    public function withdraw(string $accountNumber, Money $amount) {

        if ($amount->getAmount() <= 0) {
            throw ValidationException::withMessages(['amount' => 'Withdrawal amount must be greater than zero.']);
        }

        return DB::transaction(function () use ($accountNumber, $amount) {

            $account = Account::where('number', $accountNumber)->lockForUpdate()->first();

            if (!$account) {
                throw ValidationException::withMessages(['account' => 'Account not found.']);
            }

            if ($account->balance->lessThan($amount)) {
                throw ValidationException::withMessages(['balance' => 'Insufficient funds.']);
            }

            $account->balance = $account->balance->subtract($amount);

            $account->save();

            $account->operations()->create([
                'account_id' => $account->id,
                'type' => 'withdraw',
            ]);

            return [
                'account_number' => $account->number,
                'balance' => $account->balance,
                'currency' => $account->currency,
            ];
        });
    }


    public function  deposit(string $accountNumber, Money $amount) {
        if ($amount->isZero() || $amount->isNegative()) {
            throw ValidationException::withMessages(['amount' => 'Deposit amount must be greater than zero.']);
        }

        return DB::transaction(function () use ($accountNumber, $amount) {

            $account = Account::where('number', $accountNumber)->lockForUpdate()->first();

            if (!$account) {
                throw ValidationException::withMessages(['account' => 'Account not found.']);
            }

            if ($account->balance->getCurrency()->getCode() !== $amount->getCurrency()->getCode()) {
                throw ValidationException::withMessages(['currency' => 'Currency mismatch.']);
            }

            $account->balance = $account->balance->add($amount);

            $account->save();

            $account->operations()->create(['type' => 'deposit']);

            return [
                'account_number' => $account->number,
                'balance' => $account->balance->getAmount(),
                'currency' => $account->balance->getCurrency()->getCode(),
            ];
        });
    }

    public function balance(string $accountNumber) {
        $account = Account::where('number', $accountNumber)->first();

        if (!$account) {
            throw ValidationException::withMessages(['account' => 'Account not found.',]);
        }

        return [
            'account_number' => $account->number,
            'balance' => $account->balance->getAmount(),
            'currency' => $account->balance->getCurrency()->getCode(),
        ];
    }
}
