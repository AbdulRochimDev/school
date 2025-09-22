<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubmissionFile extends Model {
  use HasFactory;
  protected $fillable=['submission_id','file_path','mime_type','size_bytes'];
  public function submission(){ return $this->belongsTo(Submission::class); }
}
