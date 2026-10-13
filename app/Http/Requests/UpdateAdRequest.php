<?php

namespace App\Http\Requests;

class UpdateAdRequest extends StoreAdRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ad'));
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'photos'          => ['nullable', 'array', 'max:5'],
            'remove_photos'   => ['nullable', 'array'],
            'remove_photos.*' => ['integer'],
        ]);
    }
}
