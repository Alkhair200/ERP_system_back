<?php

namespace App\Models;

use DateTime;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvUoms extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }  

    public function getActiveAttribute($val)
    {
        return $val == 1 ? 'مفعل' : 'غير مفعل';
    }  


    public function getIsMasterAttribute($val)
    {
        return $val == 1 ? 'وحدة اب' : 'وحدة تجزئة';
    }  

    // public function setIsMasterAttribute($val)
    // {
    //     return $val == 'رئيسية' ? 1 : 0;
    // }      
        
    
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
}
