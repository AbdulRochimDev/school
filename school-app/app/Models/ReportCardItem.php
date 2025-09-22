<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReportCardItem extends Model
{
    protected $fillable = ['report_card_id','grade_item_id','score','weight'];
    public function reportCard(){ return $this->belongsTo(ReportCard::class); }
    public function gradeItem(){ return $this->belongsTo(GradeItem::class); }
}

