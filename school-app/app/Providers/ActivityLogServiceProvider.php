<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\ActivityLogObserver;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $observer = ActivityLogObserver::class;
        foreach ([
            \App\Models\Assignment::class,
            \App\Models\AttendanceSession::class,
            \App\Models\AttendanceRecord::class,
            \App\Models\Submission::class,
            \App\Models\Invoice::class,
            \App\Models\Payment::class,
        ] as $model) {
            if (class_exists($model)) {
                $model::observe($observer);
            }
        }
    }
}

