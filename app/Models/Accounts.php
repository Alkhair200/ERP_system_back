<?php

namespace App\Models;

use App\Models\Admin;
use App\Models\Accounts;
use App\Models\Acount_types;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accounts extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }  
    
    public function accountType()
    {
        return $this->belongsTo(Acount_types::class , 'account_type_id');
    }      

    public function paretAccountNum()
    {
        return $this->belongsTo(Accounts::class , 'parent_account_num');
    }      
}
