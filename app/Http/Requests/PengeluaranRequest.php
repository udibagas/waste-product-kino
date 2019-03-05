<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengeluaranRequest extends FormRequest
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
            'no_sj' => 'required',
            'tanggal' => 'required|date',
            'penerima' => 'required',
            'lokasi_asal' => 'required',
            'lokasi_terima' => 'required',
            'jembatan_timbang' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'no_sj' => 'Nomor Surat Jalan',
            'tanggal' => 'Tanggal',
            'penerima' => 'Nama Penerima',
            'lokasi_asal' => 'Lokasi Asal',
            'lokasi_terima' => 'Lokasi Terima',
            'jembatan_timbang' => 'Jembatan Timbang'
        ];
    }
}
