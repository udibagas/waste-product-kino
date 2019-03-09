<?php

namespace App\Listeners;

use App\Events\PengajuanPenjualanSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        // TODO: send email
    }
}
