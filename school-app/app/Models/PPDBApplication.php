<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PPDBApplication extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','status','submitted_at'];
    public function user(){ return $this->belongsTo(User::class); }
    public function documents(){ return $this->hasMany(PPDBDocument::class); }
}
