<?php

namespace App\Enums;

use App\Currency\Jpy;
use App\Currency\Twd;
use App\Currency\Usd;
use App\Interface\Currency;

enum CurrencyEnum: string
{
    case TWD = 'TWD';
    case JPY = 'JPY';
    case USD = 'USD';

    /**
     * 獲得指定幣值物件
     *
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return match ($this) {
            self::TWD => new Twd(),
            self::JPY => new Jpy(),
            self::USD => new Usd(),
        };
    }
}
