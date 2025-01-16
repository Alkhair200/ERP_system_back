<?php

namespace App\Models;

use DateTime;
use App\Models\Admin;
use App\Models\Treasuries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admins_shifts extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    } 
    
    public function treasury()
    {
        return $this->belongsTo(Treasuries::class , 'treasury_id');
    }   
    
    public function getcreatedAtAttribute($val)
    {
        $dt = new DateTime($val);
        $date = $dt->format('Y-m-d');
        $time= $dt->format('h:i');
        $newDateTime = date('A',strtotime($time));
        $newDateTimeType = (($newDateTime == "AM") ? 'صباحاً' : 'مساءً');
        return $val = $date.' '.$time.' '.$newDateTimeType;
    }
}
