<?php

namespace App\Listeners;

use App\Events\PengajuanPenjualanRejected1;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRejectedNotification1Notification
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
     * @param  PengajuanPenjualanRejected1  $event
     * @return void
     */
    public function handle(PengajuanPenjualanRejected1 $event)
    {
        //
    }
}
