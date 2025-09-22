<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;
    protected $fillable = ['class_id','student_id','term_id','enrolled_at'];

    public function class(){ return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function student(){ return $this->belongsTo(Student::class); }
}
