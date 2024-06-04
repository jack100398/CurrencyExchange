<?php

namespace Tests\Feature;

use App\Enums\CurrencyEnum;
use App\Interface\Currency;
use Tests\TestCase;

class CurrencyExchangeTest extends TestCase
{
    /** @var CurrencyEnum[]  */
    private array $currencies;

    public function setup(): void
    {
        parent::setup();

        $this->currencies = CurrencyEnum::cases();
    }

    /**
     * 驗證目前是否有幣值設定
     *
     * @return void
     */
    public function test_having_currency_setting()
    {
        $this->assertTrue(count($this->currencies) > 0);
    }

    /**
     * 驗證目前是否有合法的幣值設定, 所有幣值都應該要能互相轉換, 並且匯率皆為數字
     *
     * @return void
     */
    public function test_having_legal_currency_setting()
    {
        foreach ($this->currencies as $currency_enum) {
            $currency = $currency_enum->getCurrency();

            foreach ($this->currencies as $target) {
                $this->assertIsNumeric($currency->getExchangeRate($target->value));
            }
        }
    }

    /**
     * 基本測試, 輸入都正常的情況
     *
     * @return void
     */
    public function test_sweet()
    {
        foreach ($this->currencies as $source) {
            foreach ($this->currencies as $target) {
                $query_string = $this->buildRequestQuery($source->value, $target->value, rand(0, 999999));
                $this->get("/api/currency-exchange{$query_string}")->assertStatus(200);
            }
        }
    }

    /**
     * 測試幣值不存在狀況
     *
     * @return void 
     */
    public function test_undefined_currency()
    {
        //小寫避免隨機出現的名稱剛好是實際存在的幣值
        $wrong_currency = strtolower(fake()->name());

        //source 正確 target 錯誤
        foreach ($this->currencies as $source) {
            $query_string = $this->buildRequestQuery($source->value, $wrong_currency, rand(0, 999999));
            $this->get("/api/currency-exchange{$query_string}")->assertStatus(422);
        }

        //target 正確 source 錯誤
        foreach ($this->currencies as $target) {
            $query_string = $this->buildRequestQuery($wrong_currency, $target->value, rand(0, 999999));
            $this->get("/api/currency-exchange{$query_string}")->assertStatus(422);
        }

        //target 與 source 錯誤
        $query_string = $this->buildRequestQuery($wrong_currency, $wrong_currency, rand(0, 999999));
        $this->get("/api/currency-exchange{$query_string}")->assertStatus(422);
    }

    /**
     * 驗證合法的數值
     *
     * @return void
     */
    public function test_legal_amount()
    {
        $currencies = collect([...$this->currencies]);

        //配置測試案例的幣值
        /** @var CurrencyEnum */
        $source = $currencies->first();
        /** @var CurrencyEnum */
        $target = $currencies->last();

        $source_currency = $source->getCurrency();

        //配置測試案例的起始金額與最大金額, 從負數開始, 也可以選擇寫死案例與答案, 只是如果匯率有調整, 案例需要同步調整
        $max = 999999;
        $current = -$max;
        $step = rand(3000000000, 6000000000) / 10000; // 300000.0000 ~ 300000.0000 隨機數
        while ($current < $max) {
            $current += $step;
            $float_answer = $this->calculateExchangeAnswer($source_currency, $target->value, $current);
            $integet_answer = $this->calculateExchangeAnswer($source_currency, $target->value, (int)$current);

            //小數點兩位
            $query_string = $this->buildRequestQuery($source->value, $target->value, round($current, 2));
            $this->get("/api/currency-exchange{$query_string}")
                ->assertOk()
                ->assertJsonStructure(['msg', 'amount'])
                ->assertJson(['amount' => $float_answer]);

            //整數
            $query_string = $this->buildRequestQuery($source->value, $target->value, (int)$current);
            $this->get("/api/currency-exchange{$query_string}")
                ->assertOk()
                ->assertJsonStructure(['msg', 'amount'])
                ->assertJson(['amount' => $integet_answer]);

            //千分位＋小數
            $query_string = $this->buildRequestQuery($source->value, $target->value, number_format($current, 2));
            $this->get("/api/currency-exchange{$query_string}")
                ->assertOk()
                ->assertJsonStructure(['msg', 'amount'])
                ->assertJson(['amount' => $float_answer]);
        }
    }

    /**
     * 測試不合法的金額
     *
     * @return void
     */
    public function test_not_legal_amount()
    {
        $currencies = collect([...$this->currencies]);

        //配置測試案例的幣值
        $source = $currencies->first()->value;
        $target = $currencies->last()->value;

        $query_string = $this->buildRequestQuery($source, $target, fake()->name());
        $this->get("/api/currency-exchange{$query_string}")
            ->assertStatus(422);
    }

    /**
     * 計算匯率轉換答案
     *
     * @param Currency $currency
     * @param string $target
     * @param $amount
     *
     * @return string
     */
    private function calculateExchangeAnswer(Currency $currency, string $target, $amount): string
    {
        return number_format(round($currency->getExchangeRate($target) * $amount, 2), 2);
    }

    /**
     * 建立Query String
     *
     * @param $source
     * @param $target
     * @param $amount
     *
     * @return string
     */
    private function buildRequestQuery($source, $target, $amount): string
    {
        $params = [
            'source' => $source,
            'target' => $target,
            'amount' => $amount
        ];

        $query_string = '?';

        foreach ($params as $key => $value) {
            $query_string .= "$key=$value&";
        };

        return $query_string;
    }
}
