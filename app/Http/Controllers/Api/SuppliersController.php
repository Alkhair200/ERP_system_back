<?php

namespace App\Http\Controllers\Api;

use App\Models\Accounts;
use App\Models\Suppliers;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SuppliersCategories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\GeneralTrait;


class SuppliersController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        $com_code = Auth()->user()->com_code;

        $data = Suppliers::where('com_code',$com_code)
        ->with('admin','SupplierCategory')
        ->when($request->search, function($query) use($request){
            return $query->where('name' , 'like' , '%' .$request->search. '%')
            ->orWhere('account_num' , 'like' , '%' .$request->search. '%');

        })->orderBy('updated_at', 'desc')
        ->paginate(PAGINATION_COUNT);    

        $SuppliersCategories = SuppliersCategories::all();

        return response()->json([
            'status' => true,
            'masg'=> 'تم ارسال البيانات بنجاح',
            'suppliers'=> $data,
            'suppliersCategories'=> $SuppliersCategories,
            
         ]);        
        
    }

    public function store(Request $request)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $admin_id = Auth()->user()->id;
            $data = null;
            $data = Suppliers::where(['com_code' => $com_code ,'name' => $request->name])->first();

            $validator = Validator::make( $request->all(),[
                'supplier_name' => $data != null ? 'required|unique:suppliers,name|min:3' : 'required|min:3',
                'start_balance_status' => 'required',
                'supplier_category_id' => 'required',
                'start_balance' => 'required',
                'active' =>'required',
            ]);

            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }

            $request_data = $request->all();

            // set supplier code number
            $old_supplier_code = Suppliers::where('com_code' , $com_code)
            ->select('supplier_code')->orderBy('id','desc')->first();

            if ($old_supplier_code != null) {
                $request_data['supplier_code'] = $old_supplier_code->supplier_code +1;
            }else{
                $request_data['supplier_code'] = 1;
            }       
            
            // set account number
            $old_account = Accounts::where('com_code' , $com_code)
            ->select('account_num')->orderBy('id','desc')->first();

            if ($old_account != null) {
                $request_data['account_num'] = $old_account->account_num +1;
            }else{
                $request_data['account_num'] = 1;
            }


            if ($request_data['start_balance_status'] == 1) {
                // credit دائن
                $request_data['start_balance'] = $request_data['start_balance']*(-1);
            }else if($request_data['start_balance_status'] == 2){
                // debit مدين
                $request_data['start_balance'] = $request_data['start_balance'];
            }else if($request_data['start_balance_status'] == 3){
                // balanced متزن
                $request_data['start_balance'] = 0;
            }else{
                $request_data['start_balance_status'] = 3;
                $request_data['start_balance'] = 0;
            }

            $request_data['admin_id'] = $admin_id; 
            
            DB::beginTransaction();        
                
            Suppliers::create([
                'com_code' => $com_code,
                'name' => $request_data['supplier_name'],
                'supplier_code' => $request_data['supplier_code'],
                'supplier_category_id' => $request_data['supplier_category_id'],
                'account_num' => $request_data['account_num'],
                'start_balance_status' => $request_data['start_balance_status'],
                'start_balance' => $request_data['start_balance'],
                'active' => $request_data['active'],
                'notes' => $request_data['notes'],
                'address' => $request_data['address'],
                'date' => date('Y-m-d')
            ]);    
        
            // insert into accounts

            // set account number
            $old_account = Accounts::where('com_code' , $com_code)
            ->select('account_num')->orderBy('id','desc')->first();

            if ($old_account != null) {
                $request_data['account_num'] = $old_account->account_num +1;
            }else{
                $request_data['account_num'] = 1;
            }


            if ($request_data['start_balance_status'] == 1) {
                // credit دائن
                $request_data['start_balance'] = $request_data['start_balance']*(-1);
            }else if($request_data['start_balance_status'] == 2){
                // debit مدين
                $request_data['start_balance'] = $request_data['start_balance'];
            }else if($request_data['start_balance_status'] == 3){
                // balanced متزن
                $request_data['start_balance'] = 0;
            }else{
                $request_data['start_balance_status'] = 3;
                $request_data['start_balance'] = 0;
            }

            $request_data['admin_id'] = $admin_id;            
            $supplier_parent_account_num = AdminSetting::where('com_code' ,$com_code)->first()->supplier_parent_account_num;

            Accounts::create([
                'com_code' => $com_code,
                'name' => $request_data['supplier_name'],
                'account_num' => $request_data['account_num'],
                'parent_account_num' => $supplier_parent_account_num,
                'is_parent' => 2, // هو حساب اب
                'account_type_id' => 2, // نوع الحساب في جدول account_type  // مورد// 
                'start_balance_status' => $request_data['start_balance_status'],
                'start_balance' => $request_data['start_balance'],
                'other_lable_FK' => $request_data['supplier_code'],
                'admin_id' => $request_data['admin_id'],
                'active' => $request_data['active'],
                'notes' => $request_data['notes'],
                'date' => date('Y-m-d')
            ]);

            DB::commit();

            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }     
    }

    public function update(Request $request ,$id)
    {
        $com_code = Auth()->user()->com_code;
        $data = null;

        $data = Suppliers::where('id','!=',$id)
        ->where(['com_code' => $com_code ,'name' => $request->supplier_name])->first();        

        $validator = Validator::make( $request->all(),[
            'supplier_name' => $data != null ? 'required|unique:suppliers,name|min:3' : 'required|min:3',
            'active' =>'required',

        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }

        $supplier = Suppliers::where('com_code' ,$com_code)->find($id);

        $request_data = $request->all();

        DB::beginTransaction(); 

        $supplier->update([
            'name' => $request_data['supplier_name'],
            'supplier_category_id' => $request_data['supplier_category_id'],
            'active' => $request_data['active'],
            'notes' => $request_data['notes'],
            'address' => $request_data['address'],
        ]);    
        
        $account = Accounts::where(
            ['com_code' =>$com_code, 
            'account_num' => $request_data['account_num'],
            'account_type_id' => 2,  // نوع الحساب في جدول account_type  // مورد// 
            ])->first();


        $account->update(['name' => $request_data['supplier_name']]);

        DB::commit();

        return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
    }    

    public function destroy($id)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $records = Suppliers::where('com_code',$com_code)->find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }    


    public function updateStatus($id)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $records = Suppliers::where('com_code',$com_code)->find($id);         

            if ($records->active == 1) {
                $records->active = 0;
                $records->save();
            } else {
                $records->active = 1;
                $records->save();
            }
            return $this->returnSuccessMessage('تم تعديل الحالة بنجاح');

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }     
}
