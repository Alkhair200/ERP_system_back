<?php

namespace App\Models;

use App\Models\Jobs;
use App\Models\Admin;
use App\Models\ShiftsTypes;
use App\Models\Departements;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Emplyees extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }  
    
    public function departement()
    {
        return $this->belongsTo(Departements::class , 'departement_id');
    }      

    public function job()
    {
        return $this->belongsTo(Jobs::class , 'job_id');
    } 

    public function shift_type()
    {
        return $this->belongsTo(ShiftsTypes::class , 'shift_type_id');
    }     

    
}
