<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Models\Treasuries;
use Illuminate\Http\Request;
use App\Models\TreasuriesAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\GeneralTrait;

class AdminsController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        $com_code = Auth()->user()->com_code;

        $data = Admin::where('com_code',$com_code)
        ->when($request->search, function($query) use($request){
            return $query->where('name' , 'like' , '%' .$request->search. '%')
            ->orWhere('email' , 'like' , '%' .$request->search. '%');
        })->orderBy('updated_at', 'desc')->paginate(PAGINATION_COUNT);     
        
        return $this->returnData('admins', $data , 'تم ارسال البيانات بنجاح');         
    }

    public function details($id)
    {
        
        $com_code = Auth()->user()->com_code;

        $admin = Admin::where('com_code',$com_code)->find($id);

        $treasuries_admin = TreasuriesAdmin::with(['treasury'=>function($q){
            $q->select('id','name');
        }])
        ->where(['com_code'=>$com_code ,'admin_id'=>$id])
        ->orderBy('updated_at', 'desc')->get();

        $all_treasuries = Treasuries::select('id','name')
        ->where('active' ,1)
        ->where('com_code' ,$com_code)
        ->get();
        
        return response()->json([
            'status' => true,
            'masg'=> 'تم ارسال البيانات بنجاح',
            'admin'=> $admin,
            'treasuries_admin'=> $treasuries_admin,
            'all_treasuries'=> $all_treasuries,
            
         ]);
    } 
    
    
    public function addTreasuryToAdmin(Request $request)
    {
        $com_code = Auth()->user()->com_code;

        $admin = Admin::where('com_code',$com_code)->find($request->admin_id);

        $treasuries_admin = TreasuriesAdmin::where([
            'com_code'=>$com_code, 
            'admin_id' => $request->admin_id,
            'treasury_id' => $request->treasury_id,
            ])->first();

            if ($treasuries_admin != null) {
                return $this->returnError("E001",'الخزنة مضافه من قبل');
            }

            $validator = Validator::make( $request->all(),[
            'admin_id' => 'required',
            'treasury_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        } 

        TreasuriesAdmin::create([
            'com_code' => $com_code,
            'admin_id' => $request->admin_id,
            'treasury_id' => $request->treasury_id,
            'active' => 1,
        ]);

        return $this->returnSuccessMessage('تم اضافه الخزنة بنجاح'); 
    }

    public function updateStatus($id)
    {
        try {
            $com_code = Auth()->user()->com_code;
            $records = TreasuriesAdmin::where('com_code',$com_code)->find($id);

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

    public function adminDestroyTreasury($id)
    {
        // try {
        //     $com_code = Auth()->user()->com_code;
        //     $records = TreasuriesAdmin::where('com_code',$com_code)->find($id);
        //     $records->delete();
        //     return $this->returnSuccessMessage('تم الحذف بنجاح');

        // } catch (\Throwable $ex) {
        //     return $this->returnError(404,$ex->getMessage());
        // }
    }      
}
