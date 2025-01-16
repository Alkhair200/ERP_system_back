<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\Stores;
use App\Models\Suppliers;
use App\Models\InvItemCard;

use Illuminate\Http\Request;
use App\Models\Admins_shifts;
use Illuminate\Support\Facades\DB;
use App\Models\SuppliersWithOrders;
use App\Http\Controllers\Controller;
use App\Models\TreasuriesTransactions;
use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Suppliers_with_orders_details;
use App\Http\Requests\SuppliersWithOrdersApproveBursahseRequest;

class SuppliersWithOrdersController extends Controller
{
    use GeneralTrait;
    
    public function index(Request $request)
    {   
        $com_code = Auth()->user()->com_code;
        $data = SuppliersWithOrders::with(['store'=>function($q){
            return $q->select('id' ,'name');
        }])->where('com_code' ,$com_code)
        ->when($request->search ,function($q) use ($request){
            
            return $q->where('doc_no' ,'like','%' .$request->search. '%');
            
        })->orderBy('created_at', 'desc')->paginate(PAGINATION_COUNT);

        foreach ($data as $key => $value) {
            $supplierData = suppliers::where('supplier_code',$value->supplier_code)->first();
            $value['supplier_name'] = $supplierData->name;
        }

        $stores_data = Stores::where(['com_code'=>$com_code,'active' =>1])->get();
        $suppliers_data = Suppliers::where(['com_code'=>$com_code,'active' =>1])
        ->select('supplier_code','name')->get();
        return response()->json([
            'status' => true,
            'errNum' => "S000",
            'masg'=> 'تم ارسال البيانات بنجاح',
            'suppliers_with_orders'=> $data,
            'stores_data'=> $stores_data,
            'suppliers' => $suppliers_data
        ]);        

    } 

    public function store(Request $request)
    {
        $com_code = Auth()->user()->com_code;
        $admin_id = Auth()->user()->id;
        $data = null;
            $supplierData = Suppliers::where([
                'com_code' => $com_code ,
                'supplier_code' => $request->supplier_code
                ])->select('account_num')->first();


        $validator = Validator::make( $request->all(),[
            'supplier_code' => $data == null ? 'required|exists:suppliers,supplier_code' : 'required',
            'pill_type' => 'required',
            'order_date' => 'required',
            'store_id' =>'required',

        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }

        $request_data = $request->all();

        // set order serial number
        $auto_serial = SuppliersWithOrders::where('com_code' , $com_code)
        ->select('auto_serial')->orderBy('id','desc')->first();

        if ($auto_serial != null) {
            $request_data['auto_serial'] = $auto_serial->auto_serial +1;
        }else{
            $request_data['auto_serial'] = 1;
        }   

        $request_data['admin_id'] = $admin_id;    
        
        SuppliersWithOrders::create([
            'order_type' => 1, //فاتورة مشتريات
            'auto_serial' => $request_data['auto_serial'],
            'order_date' =>$request_data['order_date'],
            'supplier_code' => $request_data['supplier_code'],
            'account_num' => $supplierData->account_num,
            'store_id' => $request_data['store_id'],
            'doc_no' => $request_data['doc_no'],
            'pill_type' => $request_data['pill_type'],
            'admin_id' =>$request_data['admin_id'],
            'com_code' => $com_code,
            'notes' => $request_data['notes'],
        ]);

        return $this->returnSuccessMessage('تم حفظ البيانات بنجاح'); 
    }  

    public function update(Request $request, $id)
    {
        $com_code = Auth()->user()->com_code;
        $admin_id = Auth()->user()->id;
        $data = null;
            $supplierData = Suppliers::where([
                'com_code' => $com_code ,
                'supplier_code' => $request->supplier_code
                ])->select('account_num')->first();


        $validator = Validator::make( $request->all(),[
            'supplier_code' => $data == null ? 'required|exists:suppliers,supplier_code' : 'required',
            'pill_type' => 'required',
            'order_date' => 'required',
            'store_id' =>'required',

        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }

        $suppliers_with_orders = SuppliersWithOrders::where([
            'com_code' =>$com_code,
            'order_type' =>1 // اتورة مشتريات
            ])->find($id);

        $suppliers_with_orders->update([
            'order_date' =>$request->order_date,
            'supplier_code' => $request->supplier_code,
            'account_num' => $supplierData->account_num,
            'store_id' => $request->store_id,
            'doc_no' => $request->doc_no,
            'pill_type' => $request->pill_type,
            'notes' => $request->notes,
        ]);

        return $this->returnSuccessMessage('تم التعديل البيانات بنجاح');        
    }


    
    public function details(Request $request)
    {

        $com_code = Auth()->user()->com_code;
        
        $data = null;
        $data = SuppliersWithOrders::with(['store'=>function($q){
            return $q->select('id','name');
        }])->where('com_code' ,$com_code)
        ->where('id' ,$request->id)
        ->first();


        $validator = Validator::make( $request->all(),[
            'id' => $data == null ? 'required|exists:suppliers_with_orders,id' : 'required',
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }  
        
        $supplier = Suppliers::where('supplier_code',$data->supplier_code)->first();
        $data['supplier_name'] = $supplier->name;

        $details = Suppliers_with_orders_details::with(['itemCard'=>function($q){
            return $q->select('id','name','item_type');
        } , 'uoms'=>function($q){
            return $q->select('id','name');
        }])->where('supplier_with_order_id',$data->id)
        ->orderBy('updated_at','desc')->get();

        // If pill still open
        if ($data['is_approved'] == 0) {
            $inv_item_cards = InvItemCard::where(['com_code'=>$com_code,'active' =>1])->get();
        } else {
            $inv_item_cards =[];
        }
        

        $stores_data = Stores::where(['com_code'=>$com_code,'active' =>1])->get();
        $suppliers_data = Suppliers::where(['com_code'=>$com_code,'active' =>1])
        ->select('supplier_code','name')->get();
        
        return response()->json([
            'status' => true,
            'errNum' => "S000",
            'masg'=> 'تم ارسال البيانات بنجاح',
            'data'=> $data,
            'details'=> $details,
            'inv_item_cards' => $inv_item_cards,
            'stores_data' => $stores_data,
            'suppliers_data' => $suppliers_data
        ]);

    }  

    public function updateDetails(Request $request,$id)
    {
        try {
            
            $com_code = Auth()->user()->com_code;

            $details = Suppliers_with_orders_details::where([
                'com_code'=>$com_code])->find($request->id);
            $data = null;
            $data = InvItemCard::where([
                'com_code' => $com_code ,
                'id' => $request->inv_item_card_id
                ])->first();
    
            if ($request->expire_date && $request->production_date != null) {
                if ($request->expire_date < $request->production_date) {
                    # code... 
                    return $this->returnError( 'R000','لا يمكن ان يكون تاريخ الانتهاء اقل من تاريخ الانتاج!'); 
                }
            }  
    
            // item_type == 2 استهلاكي بتاريخ صلاحيه
            $validator = Validator::make( $request->all(),[
                'inv_item_card_id' => $data == null ? 'required|exists:inv_item_cards,id' : 'required',
                'quantity' => 'required|min:1',
                'uom_add_id' => 'required',
                'unit_price' => 'required|min:1',
                'production_date' => $request->item_type == 2 ? 'required' :'',
                'expire_date' => $request->item_type == 2 ? 'required' :'',
            ]);
    
            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }          
            
            $supplierWithOrders = SuppliersWithOrders::where([
                'id'=>$request->supplier_with_order_id ,
                'com_code' => $com_code,
                'order_type' => 1, // فاتورة مشتريات
                ])->select('id','is_approved','order_date','tax_value','discount_value')->first();
    
            // الفاتورة معتمدة ام غير معتمدة
            if ($supplierWithOrders->is_approved == 0) {
    
                $request_data['item_id'] = $request->inv_item_card_id;
                $request_data['uom_id'] = $request->uom_add_id;
                $request_data['deliverd_qt'] = $request->quantity;
                $request_data['unit_id'] = $request->uom_add_id;
                $request_data['unit_price'] = $request->unit_price;
                $request_data['is_parentuom'] = $request->is_parentuom;
                
                // 2=> الصنف مخزني
                if ($request->item_type == 2) {
                    $request_data['production_date'] = $request->production_date;
                    $request_data['expire_date'] = $request->expire_date;
                }
    
                $request_data['supplier_with_order_id'] = $supplierWithOrders->id;
                $request_data['total_price'] = $request->total;
                $request_data['order_date'] = $supplierWithOrders->order_date;
                $request_data['com_code'] = $com_code;
    
                DB::beginTransaction(); 
               
                $details->update($request_data);
                
                // Update parent pill
                $total_details_sum = $this->getSum(new Suppliers_with_orders_details(),'total_price',['supplier_with_order_id' => $supplierWithOrders->id]);
    
                $supplierWithOrders['total_cost_items']= $total_details_sum;
    
                // الاجمالي قبل الخصم (اجمالي الاصناف + القيمه المضافه )
                $supplierWithOrders['total_befor_discount']= $total_details_sum + $supplierWithOrders->tax_value;
                
                // الاجمالي بعد الخصم 
                $supplierWithOrders['total_cost']= $supplierWithOrders['total_befor_discount'] - $supplierWithOrders['discount_value'];
    
                $supplierWithOrders->save();
    
                DB::commit();
    
                return $this->returnSuccessMessage('تم تعديل البيانات بنجاح'); 
            }              
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
        
    }
    
    public function getUoms(Request $request)
    {
        $com_code = Auth()->user()->com_code;
        $data = InvItemCard::with(['invUom'=>function($q){
            $q->select('id','name');

        },'retalUom' => function($q){
            $q->select('id','name');

        }])->where(['id'=>$request->id,'com_code'=>$com_code])
        ->select('does_has_reta_unit','retal_uom_id','uom_id')
        ->first();

        return $this->returnData('item_card_data', $data , 'تم إرسال البيانات بنجاح');
    }

    public function newDetails(Request $request)
    {
        $admin_id = Auth()->user()->id;
        $com_code = Auth()->user()->com_code;
        
        $data = null;
        $data = InvItemCard::where(['com_code' => $com_code ,'id' => $request->inv_item_card_id])->first();

        if ($request->expire_date && $request->production_date != null) {
            if ($request->expire_date < $request->production_date) {
                # code... 
                return $this->returnError( 'R000','لا يمكن ان يكون تاريخ الانتهاء اقل من تاريخ الانتاج!'); 
            }
        }

        // item_type == 2 استهلاكي بتاريخ صلاحيه
        $validator = Validator::make( $request->all(),[
            'inv_item_card_id' => $data == null ? 'required|exists:inv_item_cards,id' : 'required',
            'quantity' => 'required|min:1',
            'uom_add_id' => 'required',
            'unit_price' => 'required|min:1',
            'production_date' => $request->item_type == 2 ? 'required' :'',
            'expire_date' => $request->item_type == 2 ? 'required' :'',
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }  

        $supplierWithOrders = SuppliersWithOrders::where(
            'id' ,$request->supplier_with_order_id
        )->select('id','is_approved','order_date','tax_value','discount_value')->first();

        // الفاتورة معتمدة ام غير معتمدة
        if ($supplierWithOrders->is_approved == 0) {

            $request_data['item_id'] = $request->inv_item_card_id;
            $request_data['uom_id'] = $request->uom_add_id;
            $request_data['deliverd_qt'] = $request->quantity;
            $request_data['unit_id'] = $request->uom_add_id;
            $request_data['unit_price'] = $request->unit_price;
            $request_data['is_parentuom'] = $request->is_parentuom;
            
            // 2=> الصنف مخزني
            if ($request->item_type == 2) {
                $request_data['production_date'] = $request->production_date;
                $request_data['expire_date'] = $request->expire_date;
            }

            $request_data['supplier_with_order_id'] = $supplierWithOrders->id;
            $request_data['total_price'] = $request->total;
            $request_data['order_date'] = $supplierWithOrders->order_date;
            $request_data['admin_id'] = $admin_id;
            $request_data['com_code'] = $com_code;

            DB::beginTransaction(); 

            Suppliers_with_orders_details::create($request_data);

            // Update parent pill
            $total_details_sum = $this->getSum(new Suppliers_with_orders_details(),'total_price',['supplier_with_order_id' => $supplierWithOrders->id]);

            $supplierWithOrders['total_cost_items']= $total_details_sum;

            // الاجمالي قبل الخصم (اجمالي الاصناف + القيمه المضافه )
            $supplierWithOrders['total_befor_discount']= $total_details_sum + $supplierWithOrders->tax_value;
            
            // الاجمالي بعد الخصم 
            $supplierWithOrders['total_cost']= $supplierWithOrders['total_befor_discount'] - $supplierWithOrders['discount_value'];

            $supplierWithOrders->save();

            DB::commit();

            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح'); 
        }
    }

    public function destroy($id)
    {
        try {

            DB::beginTransaction(); 
            
            $com_code = Auth()->user()->com_code;

            $supplierWithOrders = SuppliersWithOrders::where([
                'id'=>$id ,
                'com_code' => $com_code,
                'order_type' => 1, // فاتورة مشتريات
                ])->select('id','is_approved')->first();

            if ($supplierWithOrders->is_approved == 0) {

                $supplierWithOrders->delete();

                // Delete details pill
                $details = Suppliers_with_orders_details::where([
                    'com_code'=>$com_code,
                    'supplier_with_order_id'=>$id
                    ])->delete();

            }    

            DB::commit();

                return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }    

    public function approveInvoice(Request $request)
    {
        $com_code = Auth()->user()->com_code;
        $data = null;
        $data = SuppliersWithOrders::where([
            'com_code'=>$com_code,
            'id'=>$request->id
            ])->first();

            // current user shift
            $user_shift = $this->getUserShift(new Admins_shifts());

        if ($user_shift != null) {
            // Get treasuries balance now
            $user_shift['treasury_balance'] = 
            $this->getSum(new TreasuriesTransactions,'money',[
                'com_code'=>$com_code,
                'admin_shift_id'=>$user_shift->id
            ]) *1; 
        }    

        return response()->json([
            'status' => true,
            'errNum' => "S000",
            'masg'=> 'تم ارسال البيانات بنجاح',
            'approve_invoice'=> $data,
            'user_shift'=> $user_shift,
        ]);  

    }

    public function DoApproved(SuppliersWithOrdersApproveBursahseRequest $request)
    {
        $com_code = Auth()->user()->com_code;

        // check is not approved
        $supplierWithOrders = SuppliersWithOrders::where([
            'com_code'=>$com_code,
            'is_approved' =>1
            ])->find($request->id);

        if ($supplierWithOrders == null) {
            return $this->returnError('R001','غير قادر علي الوصول الي البيانات المطلوبه'); 
        }  
    }    
}
