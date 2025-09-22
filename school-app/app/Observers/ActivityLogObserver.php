<?php
namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class ActivityLogObserver
{
    protected function log(Model $model, string $action): void
    {
        try {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'subject_type' => get_class($model),
                'subject_id' => $model->getKey(),
                'properties' => json_encode([]),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // swallow in CLI or missing tables
        }
    }

    public function created(Model $model): void { $this->log($model, 'created'); }
    public function updated(Model $model): void { $this->log($model, 'updated'); }
    public function deleted(Model $model): void { $this->log($model, 'deleted'); }
}

