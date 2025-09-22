<?php
namespace App\Events;

use App\Models\PPDBApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class PPDBApplicationVerified
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public PPDBApplication $application)
    {
    }
}

