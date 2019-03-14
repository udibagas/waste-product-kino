<?php

namespace App\Listeners;

use App\Events\PengajuanPenjualanApproved1;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendApprovalNotification1 implements ShouldQueue
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
    public function handle(PengajuanPenjualanApproved1 $event)
    {
        //
    }
}
