<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'last_name' => 'nullable|string|max:255',
            'address1' => 'required|string|max:255',
            'apartment' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'building' => 'required|string|max:255',
            'phone' => 'required|numeric',
            'city' => 'required|string|max:100',
            'address2' => 'nullable|string|max:255',
            'postal' => 'required|numeric',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
