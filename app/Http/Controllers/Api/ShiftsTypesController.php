<?php

namespace App\Http\Controllers\Api;

use App\Models\ShiftsTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GeneralTrait;
use Validator;

class ShiftsTypesController extends Controller
{
    use GeneralTrait;
    
    public function index()
    {
        $com_code = Auth()->user()->com_code;
        $data = ShiftsTypes::with(['admin' => function($q){
            return $q->select('id','name');
        }])->where('com_code',$com_code)
           ->orderBy('created_at','desc')
           ->paginate(PAGINATION_COUNT);

        return $this->returnData('shifts_types', $data , 'تم ارسال البيانات بنجاح'); 
    }

    public function store(Request $request)
    {

        try {
            $com_code = Auth()->user()->com_code;
            $data = ShiftsTypes::where([
                'com_code' => $com_code ,
                'type' => $request->type,
                'from_time'=>$request->from_time,
                'to_time'=>$request->to_time,
                ])->first();

            $validator = Validator::make( $request->all(),[
                'type' => $data != null ? 'required|unique:shifts_types,type' : 'required',
                'from_time' => 'required',
                'to_time' => 'required',
            ]);
    
            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }
           
            $admin_id = Auth()->user()->id;

            $start_time = strtotime($request->from_time);
            $end_time = strtotime($request->to_time);

            if ($request->type == 1) {
                $name = 'صباحي';
            }else if($request->type ==2){
                $name = 'مسائي';
            }else if($request->type ==3){
                $name = 'يوم';
            }

            ShiftsTypes::create([
                'name' => $name,
                'from_time' => $request->from_time,
                'to_time' => $request->to_time,
                'type' => $request->type,
                'active' => $request->active,
                'date' => date('Y-m-d'),
                'com_code' => $com_code,
                'total_hours' => abs($end_time - $start_time) / 3600,
            ]);
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }   
    
    public function update(Request $request ,$id)
    {

        try {
            $com_code = Auth()->user()->com_code;
            $data = ShiftsTypes::where([
                'com_code' => $com_code ,
                'type' => $request->type,
                'from_time'=>$request->from_time,
                'to_time'=>$request->to_time,
                ])->where('id','!=',$id)->first();

            $validator = Validator::make( $request->all(),[
                'type' => $data != null ? 'required|unique:shifts_types,type' : 'required',
                'from_time' => 'required',
                'to_time' => 'required',
            ]);
    
            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }

            $shift_type  = ShiftsTypes::findOrFail($id);

            $start_time = strtotime($request->from_time);
            $end_time = strtotime($request->to_time);

            if ($request->type == 1) {
                $name = 'صباحي';
            }else if($request->type ==2){
                $name = 'مسائي';
            }else if($request->type ==3){
                $name = 'يوم';
            }

            $shift_type->update([
                'name' => $name,
                'from_time' => $request->from_time,
                'to_time' => $request->to_time,
                'type' => $request->type,
                'active' => $request->active,
                'total_hours' => abs($end_time - $start_time) / 3600,
            ]);            

            return $this->returnSuccessMessage('تم التعديل البيانات بنجاح');

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }   
    
    public function updateStatus($id)
    {
        try {
            $records = ShiftsTypes::findOrFail($id);

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

    public function destroy($id)
    {
        try {
            $records = ShiftsTypes::find($id)->delete();

            $counterUsed = 0;
            if ($counterUsed > 0) {
                return response()->json([
            
                    'status' => false,
                    'errNum' => "R000",
                    'msg' => 'هذه الادارة تم استخدامها من قبل. متاح فقط التعديل'
        
                ]);
            }
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }
}
