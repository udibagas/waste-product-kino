<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkemaApprovalPenjualanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'location_id' => 'required',
            'user_id' => 'required',
            'level' => 'required|in:1,2'
        ];
    }

    public function attributes()
    {
        return [
            'location_id' => 'Lokasi',
            'user' => 'User',
            'level' => 'Level'
        ];
    }
}
