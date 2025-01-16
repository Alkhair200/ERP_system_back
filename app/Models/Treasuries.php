<?php

namespace App\Models;

use DateTime;
use App\Models\Treasuries;
use App\Models\Treasuries_Delivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Treasuries extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function getupdatedAtAttribute($val)
    {
        $dt = new DateTime($val);
        $date = $dt->format('Y-m-d');
        $time= $dt->format('h:i A');
        $newDateTime = date('A',strtotime($time));
        $newDateTimeType = (($newDateTime == "AM") ? 'صباحاً' : 'مساءً');
        return $val = $date.' '.$dt->format('h:i').' '.$newDateTimeType;
    }

    public function getcreatedAtAttribute($val)
    {
        $dt = new DateTime($val);
        $date = $dt->format('Y-m-d');
        $time= $dt->format('h:i A');
        $newDateTime = date('A',strtotime($time));
        $newDateTimeType = (($newDateTime == "AM") ? 'صباحاً' : 'مساءً');
        return $val = $date.' '.$dt->format('h:i').' '.$newDateTimeType;
    }    

    public function getActiveAttribute($val)
    {
        return $val == 1 ? 'مفعل' : 'غير مفعل';
    }  

    public function getIsMasterAttribute($val)
    {
        return $val == 1 ? 'رئيسية' : 'فرعية';
    }  

    public function treasuriesDelivery()
    {
        return $this->hasMany(Treasuries_Delivery::class ,'id', 'treasury_id');
    }    
}
