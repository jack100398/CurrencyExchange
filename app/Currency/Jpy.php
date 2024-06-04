<?php

namespace App\Currency;

use App\Enums\CurrencyEnum;
use App\Exceptions\CurrencyNotExistException;
use App\Interface\Currency;

class Jpy implements Currency
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
            CurrencyEnum::TWD->value => 0.26956,
            CurrencyEnum::JPY->value => 1,
            CurrencyEnum::USD->value => 0.00885,
            default => throw new CurrencyNotExistException()
        };
    }
}
