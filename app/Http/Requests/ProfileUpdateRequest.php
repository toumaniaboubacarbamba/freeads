<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'login' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique(User::class)->ignore($this->user()->id)],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        'phone' => ['required', 'string', 'regex:/^(\+225)?[0-9]{10}$/'],
    ];
    }

    public function messages(): array
{
    return [
        'phone.regex' => 'Le numéro doit contenir 10 chiffres (ex : 0707070707) ou commencer par +225.',
    ];
}
}
