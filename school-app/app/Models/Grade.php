<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;
    protected $fillable = ['grade_item_id','student_id','score','graded_at'];
    public function gradeItem(){ return $this->belongsTo(GradeItem::class); }
    public function student(){ return $this->belongsTo(Student::class); }
}
