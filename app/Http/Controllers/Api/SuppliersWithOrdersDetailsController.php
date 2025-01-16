<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SuppliersWithOrders;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Suppliers_with_orders_details;

class SuppliersWithOrdersDetailsController extends Controller
{

    use GeneralTrait;
    
    public function destroy($id)
    {
        try {

            DB::beginTransaction(); 
            
            $com_code = Auth()->user()->com_code;

            $item_detail = Suppliers_with_orders_details::where('com_code',$com_code)->find($id);

               

            $supplierWithOrders = SuppliersWithOrders::where([
                'id'=>$item_detail->supplier_with_order_id ,
                'com_code' => $com_code,
                'order_type' => 1, // فاتورة مشتريات
                ])->select('id','is_approved')->first();

            if ($supplierWithOrders->is_approved == 0) {

                $item_detail->delete();

                // Update parent pill
                $total_details_sum = $this->getSum(new Suppliers_with_orders_details(),'total_price',['supplier_with_order_id' => $supplierWithOrders->id]);

                $supplierWithOrders['total_cost_items']= $total_details_sum;

                // الاجمالي قبل الخصم (اجمالي الاصناف + القيمه المضافه )
                $supplierWithOrders['total_befor_discount']= $total_details_sum + $supplierWithOrders->tax_value;
                
                // الاجمالي بعد الخصم 
                $supplierWithOrders['total_cost']= $supplierWithOrders['total_befor_discount'] - $supplierWithOrders['discount_value'];

                $supplierWithOrders->save();

            }    

            DB::commit();

                return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

}
