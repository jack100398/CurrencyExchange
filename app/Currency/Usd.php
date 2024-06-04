<?php

namespace App\Currency;

use App\Enums\CurrencyEnum;
use App\Exceptions\CurrencyNotExistException;
use App\Interface\Currency;

class Usd implements Currency
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
            CurrencyEnum::TWD->value => 30.444,
            CurrencyEnum::JPY->value => 111.801,
            CurrencyEnum::USD->value => 1,
            default => throw new CurrencyNotExistException()
        };
    }
}
