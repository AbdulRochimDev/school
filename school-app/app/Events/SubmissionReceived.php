<?php
namespace App\Events;

use App\Models\Submission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class SubmissionReceived
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Submission $submission)
    {
    }
}

