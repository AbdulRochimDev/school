<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model {
  protected $fillable=['term_id','class_id','class_subject_id','teacher_id','session_date','starts_at','ends_at','status','topic'];
  public function records(){ return $this->hasMany(AttendanceRecord::class); }
  public function class(){ return $this->belongsTo(SchoolClass::class, 'class_id'); }
  public function classSubject(){ return $this->belongsTo(ClassSubject::class, 'class_subject_id'); }
  public function teacher(){ return $this->belongsTo(Teacher::class, 'teacher_id'); }
}
