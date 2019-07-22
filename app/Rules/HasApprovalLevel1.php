<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\SkemaApprovalPenjualan;

class HasApprovalLevel1 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return SkemaApprovalPenjualan::where('level', 1)->where('location_id', $value)->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Skema approval pengajuan penjualan level 1 belum diset';
    }
}
