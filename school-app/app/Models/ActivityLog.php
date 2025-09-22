<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id','action','subject_type','subject_id','properties','created_at'];
    public $timestamps = false;
    public function user(){ return $this->belongsTo(User::class); }
}

