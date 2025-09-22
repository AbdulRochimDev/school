<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\AttendanceSessionClosed::class => [
            \App\Listeners\RecomputeAttendanceGrades::class,
        ],
        \App\Events\SubmissionReceived::class => [
            \App\Listeners\NotifyTeacherOnSubmission::class,
        ],
        \App\Events\GradesUpdated::class => [
            \App\Listeners\UpdateReportCards::class,
        ],
        \App\Events\PaymentVerified::class => [
            \App\Listeners\CreateLedgerEntriesOnPaymentVerified::class,
        ],
        \App\Events\PPDBApplicationVerified::class => [
            \App\Listeners\ProvisionStudentOnPPDBVerified::class,
        ],
    ];
}

