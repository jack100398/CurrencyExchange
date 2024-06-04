<?php

namespace App\Currency;

use App\Enums\CurrencyEnum;
use App\Exceptions\CurrencyNotExistException;
use App\Interface\Currency;

class Twd implements Currency
{
    /**
     * 獲得指定幣值的匯率
     *
     * @param string $taget
     *
     * @return float
     */
    public function getExchangeRate(string $target): float
    {
        return match ($target) {
            CurrencyEnum::TWD->value => 1,
            CurrencyEnum::JPY->value => 3.669,
            CurrencyEnum::USD->value => 0.03281,
            default => throw new CurrencyNotExistException()
        };
    }
}
