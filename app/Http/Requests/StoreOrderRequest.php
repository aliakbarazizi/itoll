<?php

namespace App\Http\Requests;

use App\Models\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === UserType::USER;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from.name' => 'required|string',
            'from.mobile' => 'required|string',
            'from.address' => 'required|string',
            'from.latitude' => 'required|numeric',
            'from.longitude' => 'required|numeric',
            'to.name' => 'required|string',
            'to.mobile' => 'required|string',
            'to.address' => 'required|string',
            'to.latitude' => 'required|numeric',
            'to.longitude' => 'required|numeric',
        ];
    }
}
