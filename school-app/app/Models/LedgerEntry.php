<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LedgerEntry extends Model
{
    use HasFactory;
    protected $fillable = ['ledger_id','entry_date','type','amount','reference','note'];
    public function ledger(){ return $this->belongsTo(Ledger::class); }
}
