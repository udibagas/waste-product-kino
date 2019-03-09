<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenerimaanRequest extends FormRequest
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
            'no_sj_keluar' => 'required',
            'penerima' => 'required',
            'lokasi_asal' => 'required|different:lokasi_terima',
            'lokasi_terima' => 'required|different:lokasi_asal',
        ];
    }

    public function attributes()
    {
        return [
            'no_sj' => 'Nomor Surat Jalan',
            'tanggal' => 'Tanggal',
            'no_sj_keluar' => 'Nomor Surat Jalan Keluar',
            'penerima' => 'Penerima',
            'lokasi_asal' => 'Lokasi Asal',
            'lokasi_terima' => 'Lokasi Terima',
        ];
    }
}
