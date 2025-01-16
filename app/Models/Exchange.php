<?php

namespace App\Models;

use App\Models\Admin;
use App\Models\MoveType;
use App\Models\Treasuries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exchange extends Model
{
    use HasFactory;
    protected $guarded = [];

    
    public function treasury()
    {
        return $this->belongsTo(Treasuries::class, 'treasury_id');
    }  

    
    public function moveType()
    {
        return $this->belongsTo(MoveType::class, 'move_type_id');
    }      

    

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }  

    public function adminShift()
    {
        return $this->belongsTo(Admins_shifts::class , 'admin_shift_id');
    }   
}
