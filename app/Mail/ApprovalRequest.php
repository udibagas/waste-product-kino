<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\PengajuanPenjualan;
use App\User;

class ApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuanPenjualan;

    public $level;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PengajuanPenjualan $pengajuanPenjualan, $level, User $user)
    {
        $this->pengajuanPenjualan = $pengajuanPenjualan;
        $this->level = $level;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.approval_request');
    }
}
