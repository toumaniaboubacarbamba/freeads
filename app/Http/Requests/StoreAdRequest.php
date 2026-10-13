<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // l'authentification est gérée par le middleware du controller
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'min:20', 'max:3000'],
            'price'       => ['required', 'integer', 'min:0', 'max:999999999'],
            'location'    => ['required', Rule::in(config('akwaba.locations'))],
            'condition'   => ['required', Rule::in(array_keys(config('akwaba.conditions')))],
            'photos'      => ['required', 'array', 'min:1', 'max:5'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'Ajoute au moins une photo.',
            'photos.min'      => 'Ajoute au moins une photo.',
            'photos.max'      => 'Tu peux ajouter 5 photos maximum.',
            'photos.*.image'  => 'Chaque fichier doit être une image.',
            'photos.*.mimes'  => 'Formats acceptés : JPG, PNG ou WEBP.',
            'photos.*.max'    => 'Chaque photo doit faire 2 Mo maximum.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
        ];
    }
}
