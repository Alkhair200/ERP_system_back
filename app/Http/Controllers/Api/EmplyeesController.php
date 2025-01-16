<?php

namespace App\Http\Controllers\Api;

use DB;
use DateTime;
use App\Models\Jobs;
use App\Models\Accounts;
use App\Models\Emplyees;
use App\Models\ShiftsTypes;
use App\Models\AdminSetting;
use App\Models\Departements;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\GeneralTrait;

class EmplyeesController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        $com_code = Auth()->user()->com_code;
     
        $data = Emplyees::where('com_code',$com_code)
        ->with(['admin' => function($q){
            return $q->select('id','name');
        },'job' => function($q){
            return $q->select('id','name');
        },'departement' => function($q){
            return $q->select('id','name');
        },'shift_type' => function($q){
            return $q->select('id','name');
        }])
        ->when($request->search != '', function($query) use($request){
            if ($request->search_type == 'active') {
                return $query->where('active' , 'like' ,'%' .$request->search. '%');

            }else if($request->search_type == 'start_balance_status'){
                return $query->where('start_balance_status' , 'like' , '%' .$request->search. '%');
            
            }else{
                return $query->where('name' , 'like' , '%' .$request->search. '%')
                ->orWhere('account_num' , 'like' , '%' .$request->search. '%')
                ->orWhere('employee_code' , 'like' , '%' .$request->search. '%');
            }

        })->orderBy('updated_at', 'desc')
        ->paginate(PAGINATION_COUNT);    
        
        return $this->returnData('employees', $data , 'تم ارسال البيانات بنجاح');  
    }

    public function create()
    {
        $com_code = Auth()->user()->com_code;
        $departements = Departements::where([
            'com_code' =>$com_code,
            'active' =>1,
        ])->select('id','name')->get();

        $jobs = Jobs::where([
            'com_code' =>$com_code,
            'active' =>1,
        ])->select('id','name')->get();

        $shifts_types = ShiftsTypes::where([
            'com_code' =>$com_code,
            'active' =>1,
        ])->select('id','name','from_time','to_time','type','total_hours')->get();


        foreach ($shifts_types as $value) {
            $dt = new DateTime($value->from_time);
            $time = $dt->format('h:i');
            $new_date_time = date('A',strtotime($value->from_time));
            $new_date_time_type = (($new_date_time  == "AM") ? 'صباحاً' : 'مساء');
            $value['from_time'] = $time.' '.$new_date_time_type ;

            $dt = new DateTime($value->to_time);
            $time = $dt->format('h:i');
            $new_date_time = date('A',strtotime($value->to_time));
            $new_date_time_type = (($new_date_time  == "AM") ? 'صباحاً' : 'مساء');
            $value['to_time'] = $time.' '.$new_date_time_type ;            
        }
        
        return response()->json([
            'status' => true,
            'masg'=> 'تم ارسال البيانات بنجاح',
            'departements'=> $departements,
            'jobs'=> $jobs,
            'shifts_types'=> $shifts_types,
            
         ]);        
    }

    public function store(StoreEmRequest $request)
    {
        // try {

            $com_code = Auth()->user()->com_code;
            $admin_id = Auth()->user()->id;
            $request_data = [];

            // set EM code number
            $old_employee_code = Emplyees::where('com_code' , $com_code)
            ->select('employee_code')->orderBy('id','desc')->first();

            if ($old_employee_code != null) {
                $request_data['employee_code'] = $old_employee_code->employee_code +1;
            }else{
                $request_data['employee_code'] = 1;
            }       
            
            // set account number
            $old_account = Accounts::where('com_code' , $com_code)
            ->select('account_num')->orderBy('id','desc')->first();

            if ($old_account != null) {
                $request_data['account_num'] = $old_account->account_num +1;
            }else{
                $request_data['account_num'] = 1;
            }

            $request_data['start_balance_status'] = $request->start_balance_status;

            if ($request->start_balance_status == 1) {
                // credit دائن
                $request_data['start_balance'] = $request->start_balance * (-1);
            }else if($request->start_balance_status== 2){
                // debit مدين
                $request_data['start_balance'] = $request->start_balance;
            }else if($request->start_balance_status == 3){
                // balanced متزن
                $request_data['start_balance'] = 0;
            }else{
                $request_data['start_balance_status'] = 3;
                $request_data['start_balance'] = 0;
            }

            // هل له شفت ثابت
            $request_data['do_has_shift'] = $request->do_has_shift;
            if ($request_data['do_has_shift'] == 1) {
                $request_data['shift_type_id'] = $request->shift_type_id;
                $total_hours = ShiftsTypes::where(['com_code' => $com_code,'id'=>$request_data['shift_type_id']])->select('total_hours')->first();
                
                $request_data['total_hours'] = $total_hours->total_hours;
            }else{
                $request_data['total_hours'] = $request->total_hours;
            }

            // هل له تأمين اجتماعي
            $request_data['does_has_social_insurance'] = $request->does_has_social_insurance;
            if ($request_data['does_has_social_insurance'] == 1) {
                $request_data['social_insurance_value'] = $request->social_insurance_value;
                $request_data['social_insurance_num'] =  $request->social_insurance_num;
            }   
            
            // هل له حافز شهري ثابت
            $request_data['do_has_social_motivation'] = $request->do_has_social_motivation;
            if ($request_data['do_has_social_motivation'] == 1) {
                $request_data['motivation_value'] = $request->motivation_value;
            }    
            
            // هل له بدلات شهرية ثابته
            $request_data['does_has_allowances'] = $request->does_has_allowances;
            if ($request_data['does_has_allowances']  == 1) {
                $request_data['allowances_value'] = $request->allowances_value;
            }               

            $request_data['admin_id'] = $admin_id;    

            DB::beginTransaction();        

            Emplyees::create([
                'com_code' => $com_code,
                'name' => $request->name,
                'employee_code' => $request_data['employee_code'],
                'account_num' => $request_data['account_num'],
                'start_balance_status' => $request_data['start_balance_status'],
                'start_balance' => $request_data['start_balance'],
                'do_has_shift' => $request_data['do_has_shift'],
                'shift_type_id' =>  $request_data['shift_type_id'],
                'does_has_social_insurance' => $request_data['does_has_social_insurance'],
                'social_insurance_value' =>$request_data['social_insurance_value'],
                'social_insurance_num' =>$request_data['social_insurance_num'],
                'do_has_social_motivation' =>$request_data['do_has_social_motivation'],
                'motivation_value' =>$request_data['motivation_value'],
                'does_has_allowances' =>$request_data['does_has_allowances'],
                'allowances_value' => $request_data['allowances_value'],
                'total_hours' => $request_data['total_hours'],
                'day_price' => $request->salary / 30,
                'departement_id' => $request->departement_id,
                'job_id' => $request->job_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'salary' => $request->salary,
                'current_balance' => $request_data['start_balance'],
                'active' => $request->active,
                'admin_id' => $admin_id,
                'notes' => $request->notes,
                'date' => date('Y-m-d')
            ]);


            // insert into accounts


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
            $em_parent_account_num = AdminSetting::where('com_code' ,$com_code)->first()->em_parent_account_num;

            Accounts::create([
                'com_code' => $com_code,
                'name' => $request->name,
                'account_num' => $request_data['account_num'],
                'parent_account_num' => $em_parent_account_num,
                'is_parent' => 2, // هو حساب اب
                'account_type_id' => 4, // نوع الحساب في جدول account_type // عميل //
                'start_balance_status' => $request_data['start_balance_status'],
                'start_balance' => $request_data['start_balance'],
                'other_lable_FK' => $request_data['employee_code'],
                'admin_id' => $request_data['admin_id'],
                'active' => $request->active,
                'notes' =>  $request->notes,
                'date' => date('Y-m-d')
            ]);

            DB::commit();

            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');

        // } catch (\Throwable $ex) {
        //     return $this->returnError(404,$ex->getMessage());
        // }
    }

    public function search(Request $request)
    {
      
        $com_code = Auth()->user()->com_code;

        $data = Emplyees::where('com_code',$com_code)
        ->with(['admin' => function($q){
            return $q->select('id','name');
        },'job' => function($q){
            return $q->select('id','name');
        },'departement' => function($q){
            return $q->select('id','name');
        },'shift_type' => function($q){
            return $q->select('id','name');
        }])
        ->when($request->search, function($query) use($request){
            return $query->where($request->searchType , 'like' , '%' .$request->search. '%');

        })->orderBy('updated_at', 'desc')
        ->paginate(PAGINATION_COUNT);    
        
        return $this->returnData('employees', $data , 'تم ارسال البيانات بنجاح');  

    }

    public function updateStatus($id)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $records = Emplyees::where('com_code',$com_code)->find($id);         

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
