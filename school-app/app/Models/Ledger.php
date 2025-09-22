<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ledger extends Model
{
    use HasFactory;
    protected $fillable = ['name','code'];
    public function entries(){ return $this->hasMany(LedgerEntry::class); }
}
