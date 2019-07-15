<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\PengeluaranSubmitted' => [
            'App\Listeners\UpdateStockBbPengeluaran'
        ],
        'App\Events\PenerimaanSubmitted' => [
            'App\Listeners\UpdateStockBbPenerimaan',
            'App\Listeners\SendReceivedConfirmationNotification'
        ],
        'App\Events\PenjualanSubmitted' => [
            'App\Listeners\UpdateStockPenjualan'
        ],
        'App\Events\PengajuanPenjualanSubmitted' => [
            'App\Listeners\SendApprovalRequest1Notification'
        ],
        'App\Events\PengajuanPenjualanApproved1' => [
            'App\Listeners\SendApprovalNotification1',
            'App\Listeners\SendApprovalRequest2Notification',
        ],
        'App\Events\PengajuanPenjualanApproved2' => [
            'App\Listeners\SendApprovalConfirmationNotification',
        ],
        'App\Events\PengajuanPenjualanRejected1' => [
            'App\Listeners\SendRejectedNotification1Notification',
        ],
        'App\Events\PengajuanPenjualanRejected2' => [
            'App\Listeners\SendRejectedNotification2Notification',
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
