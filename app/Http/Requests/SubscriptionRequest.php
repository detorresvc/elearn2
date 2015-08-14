<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SubscriptionRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        return [
            'credit_card_number' => 'required',
            'expiration_month' => 'required|regex:/^[0-9]{2,2}$/',
            'expiration_year' => 'required|regex:/^[0-9]{4,4}$/',
            'cvv' => 'required'
        ];
    }
}
