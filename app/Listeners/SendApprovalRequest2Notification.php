<?php

namespace App\Listeners;

use App\Events\PengajuanPenjualanApproved1;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\SkemaApprovalPenjualan;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovalRequest;

class SendApprovalRequest2Notification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PengajuanPenjualanApproved1  $event
     * @return void
     */
    public function handle(PengajuanPenjualanApproved1 $event)
    {
        $skema = SkemaApprovalPenjualan::where('level', 2)
            ->where('location_id', $event->pengajuanPenjualan->location_id)
            ->first();

        if ($skema) {
            Mail::to($skema->user)
                ->queue(new ApprovalRequest($event->pengajuanPenjualan, 2, $skema->user));
        }
    }
}
