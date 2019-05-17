<?php

namespace App\Listeners;

use App\Events\PengajuanPenjualanApproved2;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovalConfirmation;

class SendApprovalConfirmationNotification
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
     * @param  PengajuanPenjualanApproved2  $event
     * @return void
     */
    public function handle(PengajuanPenjualanApproved2 $event)
    {
        Mail::to($event->pengajuanPenjualan->user)
            ->cc('bagas@lamsolusi.com')
            ->queue(new ApprovalConfirmation($event->pengajuanPenjualan));
    }
}
