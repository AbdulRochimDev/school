<?php
namespace App\Listeners;

use App\Events\PPDBApplicationVerified;
use App\Models\{Student};

class ProvisionStudentOnPPDBVerified
{
    public function handle(PPDBApplicationVerified $event): void
    {
        $app = $event->application;
        if (!$app->user_id) return;
        Student::firstOrCreate(['user_id' => $app->user_id], ['name' => '']);
    }
}

