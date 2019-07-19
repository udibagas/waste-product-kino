<?php

namespace App\Listeners;

use App\Events\PengajuanPenjualanRejected2;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\RejectionNotification;

class SendRejectedNotification2Notification
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
     * @param  PengajuanPenjualanRejected2  $event
     * @return void
     */
    public function handle(PengajuanPenjualanRejected2 $event)
    {
        Mail::to($event->pengajuanPenjualan->user->email)
            ->queue(new RejectionNotification($event->pengajuanPenjualan));
    }
}
