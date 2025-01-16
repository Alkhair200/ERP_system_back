<?php

namespace App\Models;

use DateTime;
use App\Models\Admin;
use App\Models\Accounts;
use App\Models\Suppliers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminSetting extends Model
{
    use HasFactory;
    protected $guarded = [];


    protected $appends=['logo_path'];

    public function getLogoPathAttribute(){
        return asset('images/' .$this->logo);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }

    public function customerParentAccount()
    {
        return $this->belongsTo(Accounts::class , 'customer_parent_account_num');
    }  
    
    public function supplierParentAccount()
    {
        return $this->belongsTo(Suppliers::class , 'supplier_parent_account_num');
    } 


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

    public function getActiveAtAttribute($val)
    {

        return $val == 1 ? 'مفعل' : 'غير مفعل';

    }    

}


