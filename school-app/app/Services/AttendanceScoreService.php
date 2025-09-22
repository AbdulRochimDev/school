<?php
namespace App\Services;

use App\Models\Setting;

class AttendanceScoreService
{
    public function weights(): array
    {
        $late = $this->get('attendance.late_weight', 0.5);
        $excused = $this->get('attendance.excused_weight', 0.75);
        return [1.0, (float)$late, (float)$excused];
    }

    public function calculate(int $present, int $late, int $excused, int $total): float
    {
        if ($total <= 0) return 0.0;
        [$wPresent, $wLate, $wExcused] = $this->weights();
        $score = 100.0 * ($present*$wPresent + $late*$wLate + $excused*$wExcused) / $total;
        return round($score, 2);
    }

    protected function get(string $key, $default)
    {
        try {
            $row = Setting::where('key',$key)->first();
            return $row ? (float)$row->value : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

