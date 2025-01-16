<?php

namespace App\Models;

use DateTime;
use App\Models\Admin;
use App\Models\InvUoms;
use App\Models\Categories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvItemCard extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends=['image_path'];

    public function getImagePathAttribute()
    {
        return asset($this->image);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }  

    public function category()
    {
        return $this->belongsTo(Categories::class , 'category_id');
    }  
    
    public function invUom()
    {
        return $this->belongsTo(InvUoms::class , 'uom_id');
    }     
    
    public function retalUom()
    {
        return $this->belongsTo(InvUoms::class , 'retal_uom_id');
    }          

    

    // public function getActiveAttribute($val)
    // {
    //     return $val == 1 ? 'مفعل' : 'غير مفعل';
    // }  

    // public function getItemTypeAttribute($val)
    // {
    //     if ($val == 1) {
    //         return $val = 'مخزنى';
    //     } elseif($val == 2){
    //         return $val = 'استهلاكي';
    //     }elseif($val == 3){
    //         return $val = 'عهدة';
    //     }
    // } 
    
    
    public function getupdatedAtAttribute($val)
    {
        $dt = new DateTime($val);
        $date = $dt->format('Y-m-d');
        $time= $dt->format('h:i');
        $newDateTime = date('A',strtotime($time));
        $newDateTimeType = (($newDateTime == "AM") ? 'صباحاً' : 'مساءً');
        return $val = $date.' '.$time.' '.$newDateTimeType;
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
