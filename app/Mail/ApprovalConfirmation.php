<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\PengajuanPenjualan;

class ApprovalConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuanPenjualan;

    public $subject = '[WP APP] Approval Confirmation';

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
        return $this->markdown('emails.approval_confirmation');
    }
}
