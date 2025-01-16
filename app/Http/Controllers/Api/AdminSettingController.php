<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\Accounts;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GeneralTrait;


class AdminSettingController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    
    public function index()
    {
        $com_code = Auth()->user()->com_code;
        $data = AdminSetting::with(['customerParentAccount','supplierParentAccount','admin' => function($q){
            return $q->select('id','name');
        }])->where('com_code',$com_code)->first();

        return $this->returnData('settings', $data , 'تم ارسال البيانات بنجاح'); 
    }

    public function create()
    {
        $com_code = Auth()->user()->com_code;
        $data = Accounts::where(['is_parent'=>1,'com_code'=>$com_code])
        ->select('id','account_num', 'name')->get();     
        return $this->returnData('parentAccount', $data , 'تم ارسال البيانات بنجاح');        

    }

    // public function store(Request $request)
    // {
    //     $reques_data  = $request->all();
    //     $validator = Validator::make($request->all() ,[
    //     'system_name' => 'required|min:4',
    //     'phone' => 'required|min:4',
    //     'address' => 'required|min:4',
    //     'customer_parent_account_num' => 'required',
    //     'supplier_parent_account_num' => 'required',
    //     ]);
       
    //     if ($validator->fails()) {
    //         return $this->returnValidationError(404,$validator);
    //     }
        
    //     $com_code = Auth()->user()->com_code;

    //     if ($request->has('logo')) {

    //         $file_extension = $request->logo->getClientOriginalName();
    //         $file_name = time().'_'.$file_extension;
    //         $path = $request->logo->move('images/',$file_name);
    //         $request->logo = $path;
    //     }  

    //     $com_code = Auth()->user()->com_code;
    //     $admin_id = Auth()->user()->id;

    //     $data = AdminSetting::create([
    //         'system_name' => $request->system_name,
    //         'phone' => $request->phone,
    //         'customer_parent_account_num' => $request->customer_parent_account_num,
    //         'supplier_parent_account_num' => $request->supplier_parent_account_num,
    //         'address' => $request->address,
    //         'logo' => $request->logo,
    //         'com_code' => $com_code,
    //         'admin_id' => $admin_id,
    //         'general_alert' => $request->general_alert,
    //         'active' => $request->active,
    //     ]);

    //     return $this->returnData('settings', $data , 'تم الحفظ البيانات بنجاح');
    // }
    


    public function update(Request $request, $id)
    {
        
        
        $reques_data  = $request->all();
        $validator = Validator::make($request->all() ,[
        'system_name' => 'required|min:4',
        'phone' => 'required|min:4',
        'address' => 'required|min:4',
        'customer_parent_account_num' => 'required',
        'supplier_parent_account_num' => 'required',
        'em_parent_account_num' => 'required'
        ]);


        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }

        $com_code = Auth()->user()->com_code;

        $data = AdminSetting::where(['id'=>$id,'com_code'=>$com_code])->first();

        if ($request->logo != null) {
            
            if ($data->logo != null && $data->logo != 'default.png') {
                unlink($data->logo);
            }

            $file_extension = $request->logo->getClientOriginalName();
            $file_name = time().'_'.$file_extension;
            $path = $request->logo->move('images/',$file_name);

            $data->update(['logo' => $path]);
        }

        $data->update([
            'system_name' => $request->system_name,
            'phone' => $request->phone,
            'customer_parent_account_num' => $request->customer_parent_account_num,
            'supplier_parent_account_num' => $request->supplier_parent_account_num,
            'em_parent_account_num' => $request->em_parent_account_num,
            
            'address' => $request->address,
            'general_alert' => $request->general_alert,
            'active' => $request->active,
            'com_code' => $request->com_code,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        // return $this->returnData('settings', $data , 'تم تعديل البيانات بنجاح');
        return $this->returnSuccessMessage('تم التعديل بنجاح'); 
    }

    public function destroy(Request $request)
    {
        # code...
    }
}
