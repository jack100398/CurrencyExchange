<?php

namespace App\Http\Requests;

use App\Enums\CurrencyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CurrencyExchangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'source' => ['required', 'string', new Enum(CurrencyEnum::class)],
            'target' => ['required', 'string', new Enum(CurrencyEnum::class)],
            'amount' => ['required', 'numeric']
        ];
    }

    /**
     * 準備驗證參數
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        //千分位取代, 轉化為一般數字
        $params = $this->all();
        $params['amount'] = str_replace(',', '', $this->amount);

        $this->replace($params);
    }
}
