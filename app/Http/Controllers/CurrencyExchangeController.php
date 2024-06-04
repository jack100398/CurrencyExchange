<?php

namespace App\Http\Controllers;

use App\Enums\CurrencyEnum;
use App\Http\Requests\CurrencyExchangeRequest;
use App\Http\Services\CurrencyExchangeService;
use Illuminate\Http\JsonResponse;

class CurrencyExchangeController
{
    /**
     * 幣值轉換Api
     *
     * @param CurrencyExchangeRequest $request
     *
     * @return JsonResponse
     */
    public function __invoke(CurrencyExchangeRequest $request): JsonResponse
    {
        $params = $request->validated();

        $currency = CurrencyEnum::from($params['source'])->getCurrency();

        $exchange_service = new CurrencyExchangeService($currency);

        $target_amount = $exchange_service->exchange($params['target'], $params['amount']);

        return response()->json(
            [
                'msg'    => 'success',
                'amount' => number_format(round($target_amount, 2), 2)
            ]
        );
    }
}
