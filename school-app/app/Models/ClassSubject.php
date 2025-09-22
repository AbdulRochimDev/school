<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassSubject extends Model
{
    use HasFactory;
    protected $fillable = ['class_id','subject_id','teacher_id'];

    public function class(){ return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function subject(){ return $this->belongsTo(Subject::class); }
    public function teacher(){ return $this->belongsTo(Teacher::class); }
    public function assignments(){ return $this->hasMany(Assignment::class); }
    public function gradeItems(){ return $this->hasMany(GradeItem::class); }
    public function attendanceSessions(){ return $this->hasMany(AttendanceSession::class); }
}
