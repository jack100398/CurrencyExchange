<?php

namespace App\Http\Services;

use App\Interface\Currency;

class CurrencyExchangeService
{
    private Currency $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * 幣值轉換
     *
     * @param string $target
     * @param float $amount
     *
     * @return float
     */
    public function exchange(string $target, float $amount): float
    {
        $rate = $this->currency->getExchangeRate($target);

        return $amount * $rate;
    }
}
