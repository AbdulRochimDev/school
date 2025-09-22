<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = ['class_subject_id','title','description','due_at','max_score'];

    public function classSubject(){ return $this->belongsTo(ClassSubject::class); }
    public function submissions(){ return $this->hasMany(Submission::class); }
}
