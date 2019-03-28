<?php

namespace App\Listeners;

use App\Events\PenerimaanSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ReceiptNotification;
use App\Pengeluaran;

class SendReceivedConfirmationNotification
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
     * @param  PenerimaanSubmitted  $event
     * @return void
     */
    public function handle(PenerimaanSubmitted $event)
    {
        $pengeluaran = Pengeluaran::where('no_sj', $event->penerimaan->no_sj_keluar)->first();

        if ($pengeluaran) {
            Mail::to($event->pengeluaran->user)
                ->cc('bagas@lamsolusi.com')
                ->queue(new ReceiptNotification($event->penerimaan));
        }
    }
}
