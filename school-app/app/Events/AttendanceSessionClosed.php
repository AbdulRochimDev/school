<?php
namespace App\Events;

use App\Models\AttendanceSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class AttendanceSessionClosed
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public AttendanceSession $session)
    {
    }
}

