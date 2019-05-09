<?php

namespace App\Listeners;

use App\Events\PenerimaanSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ReceiptNotification;
use App\Pengeluaran;
use Illuminate\Support\Facades\Mail;

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
        if ($event->penerimaan->pengeluaran) {
            Mail::to($event->penerimaan->pengeluaran->user->email)
                ->to($event->penerimaan->user->email)
                ->cc('bagas@lamsolusi.com')
                ->subject('[WP APP] Receipt Notification')
                ->queue(new ReceiptNotification($event->penerimaan));
        }
    }
}
