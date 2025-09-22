<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','class_id','nis','nisn','name'];

    public function user(){ return $this->belongsTo(User::class); }
    public function class(){ return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function enrollments(){ return $this->hasMany(Enrollment::class); }
    public function submissions(){ return $this->hasMany(Submission::class); }
    public function attendanceRecords(){ return $this->hasMany(AttendanceRecord::class); }
    public function grades(){ return $this->hasMany(Grade::class); }
}
