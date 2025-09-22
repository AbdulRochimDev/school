<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolClass extends Model
{
    use HasFactory;
    protected $table = 'classes';
    protected $fillable = ['name','level','homeroom_teacher_id'];

    public function students(){ return $this->hasMany(Student::class, 'class_id'); }
    public function enrollments(){ return $this->hasMany(Enrollment::class, 'class_id'); }
    public function classSubjects(){ return $this->hasMany(ClassSubject::class, 'class_id'); }
}
