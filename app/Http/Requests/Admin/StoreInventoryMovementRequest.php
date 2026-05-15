<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => ['required', 'in:restock,adjustment'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'adjustment_direction' => ['nullable', 'in:add,subtract'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
