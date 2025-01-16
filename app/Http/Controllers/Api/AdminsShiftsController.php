<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Admins_shifts;
use App\Models\TreasuriesAdmin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GeneralTrait;
use Validator;

class AdminsShiftsController extends Controller
{
    use GeneralTrait;
    
    public function index(Request $request)
    {
        $com_code = Auth()->user()->com_code;
        $admin_id = Auth()->user()->id;
        $data = Admins_shifts::with('admin','treasury')->where('com_code',$com_code)
        ->with(['treasury','admin' => function($q){
            return $q->select('id','name');

        }])->when($request->search, function($query) use($request){
            return $query->where('admin_id' , 'like' , '%' .$request->search. '%');
            // ->orWhere('account_num' , 'like' , '%' .$request->search. '%');

        })->orderBy('updated_at', 'desc')
        ->paginate(PAGINATION_COUNT);

        $treasuries_admins = TreasuriesAdmin::with(['treasury'=>function($q){
            $q->select('id','name');
        }])->where(['com_code'=>$com_code, 'active'=>1,'admin_id'=>$admin_id])->get();

        foreach ($treasuries_admins as $key => $value) {

            // الخزنه موجوده ولا يعمل عليها مستخدم اخر
            $check_exsits_admin_shift = Admins_shifts::where([
                'com_code'=>$com_code,
                'treasury_id'=>$value->treasury_id,
                'is_finished' =>0
                ])->first();

                // اذا كانت شغاله مع مستخدم اخر
            if ($check_exsits_admin_shift != null) {
                $value['avaliable'] = false;
            } else {
                $value['avaliable'] = true;
            }    
        }
        // اذا كان المستخدم لديه شفت مفتوح حاليا لا يمكن فتح شفت اخر 
        $check_exsits_open_shift = Admins_shifts::where([
            'com_code'=>$com_code,
            'admin_id'=>$admin_id,
            'is_finished' =>0
            ])->first();        

        return response()->json([
            'status' => true,
            'errNum' => "S000",
            'masg'=> 'تم ارسال البيانات بنجاح',
            'admins_shifts'=> $data,
            'treasuries_admins'=> $treasuries_admins,
            'check_exsits_open_shift' =>$check_exsits_open_shift
        ]);         

    }

    public function store(Request $request)
    {
        $com_code = Auth()->user()->com_code;
        $admin_id = Auth()->user()->id;

        $validator = Validator::make( $request->all(),[
            'treasury_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }

        $check_exsits_open_shift = Admins_shifts::where([
            'com_code'=>$com_code,
            'admin_id'=>$admin_id,
            'is_finished' =>0
            ])->first();

        if ($check_exsits_open_shift != null) {
            return response()->json([
                'status' => false,
                'errNum' => 'R000',
                'errors' => 'هنالك شفت مفتوح بالفعل لديك حاليا ولا يمكن فتح شفت جديد إلا بعد إغلاق الشفت الحالي'
            ]);
        }   

        Admins_shifts::create([
            'treasury_id' => $request->treasury_id,
            'admin_id' => $admin_id,
            'com_code' => $com_code,
            'date' => date('Y-m-d'),
            'start_date' => date('Y-m-d H:i:s'),
        ]);

        return $this->returnSuccessMessage('تم الحفظ بنجاح');

    }
}
