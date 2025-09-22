<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentVerification extends Model
{
    use HasFactory;
    protected $fillable = ['payment_id','verified_by','verified_at','status','note'];
    public function payment(){ return $this->belongsTo(Payment::class); }
}
