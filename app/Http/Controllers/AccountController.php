<?php

namespace App\Http\Controllers;

use App\Http\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Money\Currency;
use Money\Money;

class AccountController extends Controller
{
    public function __construct(private AccountService $accountService)
    {}

    public function withdraw(string $account, Request $request) {
        try {
            $request->validate([
                'amount' => ['required', 'numeric', 'min:100'],
                'currency' => ['string', 'size:3'],
            ]);

            $amount = new Money(
                $request->input('amount'),
                new Currency($request->input('currency', 'BRL'))
            );

            return response()->json($this->accountService->withdraw(
                $account, 
                $amount
            ), Response::HTTP_OK);


        } catch (ValidationException $e) {
            return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function deposit(string $account,Request $request) {
        try {
            $request->validate([
                'amount' => ['required', 'numeric', 'min:100'],
                'currency' => ['string', 'size:3'],
            ]);

            $amount = new Money(
                $request->input('amount'),
                new Currency($request->input('currency', 'BRL'))
            );

            return response()->json($this->accountService->deposit(
                $account, 
                $amount
            ), Response::HTTP_OK);

        } catch (ValidationException $e) {
            return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function balance(string $account) {
        try {
            $result = $this->accountService->balance($account);

            return response()->json($result);

        } catch (ValidationException $e) {
            return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
