<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'title' => ['required', 'max:255'],
            'starts_at' => ['required', 'date_format:Y-m-d H:i'],
            'ends_at' => ['required', 'date_format:Y-m-d H:i', 'after:starts_at'], // nullable et format strict

        ];
    }
}
