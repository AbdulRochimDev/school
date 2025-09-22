<?php
namespace App\Listeners;

use App\Events\GradesUpdated;
use App\Models\{ReportCard, ReportCardItem, GradeItem, Grade};

class UpdateReportCards
{
    public function handle(GradesUpdated $event): void
    {
        $studentIds = array_values(array_unique($event->studentIds));
        if (empty($studentIds)) {
            return;
        }

        $gradeItems = GradeItem::query()->get()->keyBy('id');
        if ($gradeItems->isEmpty()) {
            return;
        }

        $gradesByStudent = Grade::query()
            ->whereIn('student_id', $studentIds)
            ->whereIn('grade_item_id', $gradeItems->keys())
            ->get()
            ->groupBy('student_id');

        foreach ($studentIds as $studentId) {
            $reportCard = ReportCard::firstOrCreate(['student_id' => $studentId, 'term_id' => $event->termId]);

            $totalWeightedScore = 0.0;
            $totalWeight = 0.0;

            foreach ($gradesByStudent->get($studentId, collect()) as $grade) {
                $gradeItem = $gradeItems->get($grade->grade_item_id);
                if (!$gradeItem) {
                    continue;
                }

                $weight = (float) ($gradeItem->weight ?? 0);

                ReportCardItem::updateOrCreate(
                    ['report_card_id' => $reportCard->id, 'grade_item_id' => $gradeItem->id],
                    ['score' => $grade->score, 'weight' => $weight]
                );

                if ($weight > 0) {
                    $totalWeightedScore += $grade->score * $weight;
                    $totalWeight += $weight;
                }
            }

            if ($totalWeight > 0) {
                $reportCard->final_score = round($totalWeightedScore / $totalWeight, 2);
                $reportCard->save();
            }
        }
    }
}

