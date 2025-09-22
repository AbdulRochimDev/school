<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['student_id','number','amount','status','due_date','issued_at'];
    public function student(){ return $this->belongsTo(Student::class); }
    public function payments(){ return $this->hasMany(Payment::class); }
}
