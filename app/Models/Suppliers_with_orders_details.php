<?php

namespace App\Models;

use App\Models\InvUoms;
use App\Models\InvItemCard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Suppliers_with_orders_details extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }  

    public function itemCard()
    {
        return $this->belongsTo(InvItemCard::class , 'item_id');
    }      

    public function uoms()
    {
        return $this->belongsTo(InvUoms::class , 'uom_id');

    }

    
}
