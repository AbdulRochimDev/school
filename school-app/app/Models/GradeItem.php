<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeItem extends Model
{
    use HasFactory;
    protected $fillable = ['class_subject_id','name','weight','max_score'];
    public function classSubject(){ return $this->belongsTo(ClassSubject::class); }
    public function grades(){ return $this->hasMany(Grade::class); }
}
