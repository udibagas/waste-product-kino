<?php

namespace App\Listeners;

use App\Events\PengajuanPenjualanSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovalRequest;
use App\SkemaApprovalPenjualan;

class SendApprovalRequest1Notification implements ShouldQueue
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
     * @param  PengajuanPenjualanSubmitted  $event
     * @return void
     */
    public function handle(PengajuanPenjualanSubmitted $event)
    {
        $skema = SkemaApprovalPenjualan::where('level', 1) 
            ->where('location_id', $event->pengajuanPenjualan->location_id)
            ->first();

        if ($skema) {
            Mail::to($skema->user)
                ->cc('bagas@lamsolusi.com')
                ->queue(new ApprovalRequest($event->pengajuanPenjualan, 1, $skema->user));
        }
    }
}
