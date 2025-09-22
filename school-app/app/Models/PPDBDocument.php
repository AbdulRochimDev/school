<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PPDBDocument extends Model
{
    use HasFactory;
    protected $fillable = ['ppdb_application_id','type','file_path','verified_at'];
    public function application(){ return $this->belongsTo(PPDBApplication::class, 'ppdb_application_id'); }
}
