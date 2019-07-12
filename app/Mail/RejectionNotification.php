<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\PengajuanPenjualan;

class RejectionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuanPenjualan;

    public $subject = '[WP APP] Rejection Notification';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PengajuanPenjualan $pengajuanPenjualan)
    {
        $this->pengajuanPenjualan = $pengajuanPenjualan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.rejection_notification');
    }
}
