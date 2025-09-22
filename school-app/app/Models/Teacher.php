<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','nip','name'];

    public function user(){ return $this->belongsTo(User::class); }
    public function classSubjects(){ return $this->hasMany(ClassSubject::class); }
    public function attendanceSessions(){ return $this->hasMany(AttendanceSession::class, 'teacher_id'); }
}
