<?php

namespace App\Models;

use App\Models\Admin;
use App\Models\SuppliersCategories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Suppliers extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }  

    public function SupplierCategory()
    {
        return $this->belongsTo(SuppliersCategories::class , 'supplier_category_id');
    }          
}
