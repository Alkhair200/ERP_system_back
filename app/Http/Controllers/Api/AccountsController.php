<?php

namespace App\Http\Controllers\Api;

use App\Models\Accounts;
use App\Models\Customers;
use App\Models\Acount_types;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\GeneralTrait;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{

    use GeneralTrait;

    public function index(Request $request)
    {
        $com_code = Auth()->user()->com_code;

        $data = Accounts::where('com_code',$com_code)
        ->with(['admin','accountType'=> function($q){
            return $q->select('id','name');
        },'paretAccountNum' => function($q){
            return $q->select('id','name');
        }])
        ->when($request->search, function($query) use($request){
            return $query->where('name' , 'like' , '%' .$request->search. '%')
            ->orWhere('account_num' , 'like' , '%' .$request->search. '%');

        })->orderBy('updated_at', 'desc')
        ->paginate(PAGINATION_COUNT);     
        
        return $this->returnData('accounts', $data , 'تم ارسال البيانات بنجاح');         

    }

    public function create()
    {
        $com_code = Auth()->user()->com_code;

        $accountTypes = Acount_types::where(['active'=>1,'related_internal_account'=>0])
        ->select('id', 'name')->get();
        
        $parentAccount = Accounts::where(['is_parent'=>1,'com_code'=>$com_code])
        ->select('id','account_num', 'name')->get();            

        return response()->json([
            'status' => true,
            'masg'=> 'تم ارسال البيانات بنجاح',
            'accountTypes'=> $accountTypes,
            'parentAccount' =>$parentAccount,
            ]);  
    }

    public function store(Request $request)
    {
        $com_code = Auth()->user()->com_code;
        $admin_id = Auth()->user()->id;
        $data = null;
        $data = Accounts::where(['com_code' => $com_code ,'name' => $request->balance_name])->first();

        $validator = Validator::make( $request->all(),[
            'balance_name' => $data != null ? 'required|unique:accounts,name|min:3' : 'required|min:3',
            'is_parent' => 'required',
            'parent_account_num' => $request->is_parent == 2 ? 'required' : '',
            'start_balance_status' => 'required',
            'start_balance' =>'required',
            'account_type' => 'required',  

            ]);

            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }

            $request_data = $request->all();

            // 2 balance is ab
            if ($request->is_parent == 1) {
                $request_data['parent_account_num ']= '';
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
                // credit
                $request_data['start_balance'] = $request_data['start_balance']*(-1);
            }else if($request_data['start_balance_status'] == 2){
                // debit
                $request_data['start_balance'] = $request_data['start_balance'];
            }else if($request_data['start_balance_status'] == 3){
                // balanced
                $request_data['start_balance'] = 0;
            }else{
                $request_data['start_balance_status'] = 3;
                $request_data['start_balance'] = 0;
            }

            $request_data['admin_id'] = $admin_id;

            Accounts::create([
                'com_code' => $com_code,
                'name' => $request_data['balance_name'],
                'account_type_id' => $request_data['account_type'],
                'account_num' => $request_data['account_num'],
                'parent_account_num' => $request_data['parent_account_num'],
                'is_parent' => $request_data['is_parent'],
                'start_balance_status' => $request_data['start_balance_status'],
                'start_balance' => $request_data['start_balance'],
                'admin_id' => $request_data['admin_id'],
                'active' => $request_data['active'],
                'notes' => $request_data['notes'],
                'date' => date('Y-m-d')
            ]);

            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');

    }


    public function edit($id)
    {
        $com_code = Auth()->user()->com_code;
        $data = Accounts::where('com_code' ,$com_code)->find($id);
        $accountTypes = Acount_types::where(['active'=>1,'related_internal_account'=>0])
        ->select('id', 'name')->get();
        
        $parentAccount = Accounts::where(['is_parent'=>1,'com_code'=>$com_code])
        ->select('id','account_num', 'name')->get();            

        return response()->json([
            'status' => true,
            'masg'=> 'تم ارسال البيانات بنجاح',
            'account'=> $data,
            'accountTypes'=> $accountTypes,
            'parentAccount' =>$parentAccount,
            ]);    
    }

    public function update(Request $request, $id)
    {
        try {
            
            $com_code = Auth()->user()->com_code;

            $account = Accounts::where('com_code' ,$com_code)->find($id);
            $data = null;
            
            $data = Accounts::where('id','!=',$id)
            ->where(['com_code' => $com_code ,'name' => $request->balance_name])->first();
    
            $validator = Validator::make( $request->all(),[
                'balance_name' => $data != null ? 'required|unique:accounts,name|min:3' : 'required|min:3',
                'is_parent' => 'required',
                'parent_account_num' => $request->is_parent == 2 ? 'required' : '',
                'start_balance_status' => 'required',
                'start_balance' =>'required',
                'account_type' => 'required',  
    
            ]);
    
            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }
    
            $request_data = $request->all();
    
            // 2 balance is ab
            if ($request->is_parent == 1) {
                $request_data['parent_account_num ']= '';
            }
    
    
            if ($request_data['start_balance_status'] == 1) {
                // credit
                $request_data['start_balance'] = $request_data['start_balance']*(-1);
            }else if($request_data['start_balance_status'] == 2){
                // debit
                $request_data['start_balance'] = $request_data['start_balance'];
            }else if($request_data['start_balance_status'] == 3){
                // balanced
                $request_data['start_balance'] = 0;
            }else{
                $request_data['start_balance_status'] = 3;
                $request_data['start_balance'] = 0;
            }
    
            // هل الحساب اب 1-نعم 2-لا
            if ($request_data['is_parent'] == 1) {
                $request_data['parent_account_num'] = null;
            }      

            DB::beginTransaction(); 

            $account->update([
                'name' => $request_data['balance_name'],
                'account_type_id' => $request_data['account_type'],
                'parent_account_num' => $request_data['parent_account_num'],
                'is_parent' => $request_data['is_parent'],
                'start_balance_status' => $request_data['start_balance_status'],
                'start_balance' => $request_data['start_balance'],
                'active' => $request_data['active'],
                'notes' => $request_data['notes'],
            ]);

            if ($account->account_type_id == 3) {
                
                $customer = Customers::where(
                    ['com_code' =>$com_code, 
                    'id' => $account->other_lable_FK,
                    'account_num' => $account->account_num
                    ])->first();      

                $customer->update(['name' => $request_data['balance_name']]);   
            }    

            DB::commit();        
    
            return $this->returnSuccessMessage('تم تعديل البيانات بنجاح'); 

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }  
    
    
    public function destroy($id)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $records = Accounts::where('com_code',$com_code)->find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }    


    public function updateStatus($id)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $records = Accounts::where('com_code',$com_code)->find($id);         

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
