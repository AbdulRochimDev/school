<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{
    use HasFactory;
    protected $fillable = ['assignment_id','student_id','content','submitted_at','score','feedback'];

    public function assignment(){ return $this->belongsTo(Assignment::class); }
    public function student(){ return $this->belongsTo(Student::class); }
    public function files(){ return $this->hasMany(SubmissionFile::class); }
}
