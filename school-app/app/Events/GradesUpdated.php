<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class GradesUpdated
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public array $studentIds, public ?int $termId = null)
    {
    }
}

