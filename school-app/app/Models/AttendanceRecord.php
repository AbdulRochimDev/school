<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model {
  protected $fillable=['attendance_session_id','student_id','status','checkin_at','note'];
  public function session(){ return $this->belongsTo(AttendanceSession::class,'attendance_session_id'); }
  public function student(){ return $this->belongsTo(Student::class); }
}
