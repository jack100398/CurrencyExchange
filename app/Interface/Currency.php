<?php

namespace App\Interface;

interface Currency
{
    /**
     * 獲得指定幣值的匯率
     *
     * @param string $taget
     *
     * @return float
     */
    public function getExchangeRate(string $target): float;
}
