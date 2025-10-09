<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShippingCarrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('shipping_carrier');
        return [
            'name' => [
                'required','max:50',
                Rule::unique('shipping_carriers','name')->ignore($id)
            ],
        ];
    }
}
