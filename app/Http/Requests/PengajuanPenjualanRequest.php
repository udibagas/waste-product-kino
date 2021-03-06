<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\HasApprovalLevel1;
use App\Rules\HasApprovalLevel2;

class PengajuanPenjualanRequest extends FormRequest
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
            'tanggal' => 'required',
            'no_aju' => 'required',
            'location_id' => ['required', new HasApprovalLevel1, new HasApprovalLevel2],
            'period' => 'required_if:jenis,WP|array',
            'jenis' =>'required',
        ];
    }

    public function attributes()
    {
        return [
            'tanggal' => 'Tanggal',
            'no_aju' => 'No. Pengajuan',
            'location_id' => 'Lokasi',
            'plant' => 'Plant',
            'period' => 'Periode',
            'jenis' =>'Jenis '
        ];
    }
}
