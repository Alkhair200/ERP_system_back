<?php

namespace App\Http\Controllers\Api;

use App\Models\Accounts;
use App\Models\Customers;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\GeneralTrait;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        $com_code = Auth()->user()->com_code;

        $data = Customers::where('com_code',$com_code)
        ->with('admin')
        ->when($request->search, function($query) use($request){
            return $query->where('name' , 'like' , '%' .$request->search. '%')
            ->orWhere('account_num' , 'like' , '%' .$request->search. '%');

        })->orderBy('updated_at', 'desc')
        ->paginate(PAGINATION_COUNT);    
        
        return $this->returnData('customers', $data , 'تم ارسال البيانات بنجاح');  
    }

    public function store(Request $request)
    {
        try {

            $com_code = Auth()->user()->com_code;
            $admin_id = Auth()->user()->id;
            $data = null;
            $data = Customers::where(['com_code' => $com_code ,'name' => $request->name])->first();
    
            $validator = Validator::make( $request->all(),[
                'customer_name' => $data != null ? 'required|unique:customers,name|min:3' : 'required|min:3',
                'start_balance_status' => 'required',
                'start_balance' => 'required',
                'active' =>'required',
    
            ]);

            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }

            $request_data = $request->all();

            // set customer code number
            $old_customer_code = Customers::where('com_code' , $com_code)
            ->select('customer_code')->orderBy('id','desc')->first();

            if ($old_customer_code != null) {
                $request_data['customer_code'] = $old_customer_code->customer_code +1;
            }else{
                $request_data['customer_code'] = 1;
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
            
            Customers::create([
                'com_code' => $com_code,
                'name' => $request_data['customer_name'],
                'customer_code' => $request_data['customer_code'],
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
            $customer_parent_account_num = AdminSetting::where('com_code' ,$com_code)->first()->customer_parent_account_num;

            Accounts::create([
                'com_code' => $com_code,
                'name' => $request_data['customer_name'],
                'account_num' => $request_data['account_num'],
                'parent_account_num' => $customer_parent_account_num,
                'is_parent' => 2, // هو حساب اب
                'account_type_id' => 3, // نوع الحساب في جدول account_type // عميل //
                'start_balance_status' => $request_data['start_balance_status'],
                'start_balance' => $request_data['start_balance'],
                'other_lable_FK' => $request_data['customer_code'],
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

        $data = Customers::where('id','!=',$id)
        ->where(['com_code' => $com_code ,'name' => $request->customer_name])->first();        

        $validator = Validator::make( $request->all(),[
            'customer_name' => $data != null ? 'required|unique:customers,name|min:3' : 'required|min:3',
            'active' =>'required',

        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }

        $customer = Customers::where('com_code' ,$com_code)->find($id);

        $request_data = $request->all();

        DB::beginTransaction(); 

        $customer->update([
            'name' => $request_data['customer_name'],
            'active' => $request_data['active'],
            'notes' => $request_data['notes'],
            'address' => $request_data['address'],
        ]);    
        
        $account = Accounts::where(
            ['com_code' =>$com_code, 
            'account_num' => $request_data['account_num'],
            'account_type_id' => 3,
            ])->first();


        $account->update(['name' => $request_data['customer_name']]);

        DB::commit();

        return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
    }
    
    public function destroy($id)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $records = Customers::where('com_code',$com_code)->find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }    


    public function updateStatus($id)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $records = Customers::where('com_code',$com_code)->find($id);         

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
