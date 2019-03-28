<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Penerimaan;

class ReceiptNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $penerimaan;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Penerimaan $penerimaan)
    {
        $this->penerimaan = $penerimaan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.receipt_notification');
    }
}
