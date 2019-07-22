<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenjualanRequest extends FormRequest
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
            'tanggal' => 'required|date',
            'no_sj' => 'required',
            'top_date' => 'required|date',
            'pembeli_id' => 'required'

        ];
    }

    public function attributes()
    {
        return [
            'tanggal' => 'Tanggal',
            'no_sj' => 'Nomor Surat Jalan',
            'top_date' => 'TOP Date'
        ];
    }
}
