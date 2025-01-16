<?php

namespace App\Http\Controllers\Api;

use App\Models\Departements;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GeneralTrait;
use Validator;

class DepartementsController extends Controller
{
    use GeneralTrait;

    public function index()
    {
        $com_code = Auth()->user()->com_code;
        $data = Departements::with(['admin' => function($q){
            return $q->select('id','name');
        }])->where('com_code',$com_code)->orderBy('created_at','desc')->paginate(PAGINATION_COUNT);

        return $this->returnData('departements', $data , 'تم ارسال البيانات بنجاح'); 
    }

    public function store(Request $request)
    {

        try {
            $com_code = Auth()->user()->com_code;
            $data = Departements::where(['com_code' => $com_code ,'name' => $request->name])->first();

            $validator = Validator::make( $request->all(),[
                'name' => $data != null ? 'required|min:3|unique:departements,name' : 'required|min:3',
            ]);
    
            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }

            $request['com_code'] = Auth()->user()->com_code;
            $request['admin_id'] = Auth()->user()->id;

            Departements::create($request->except(['token']));
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }   
    
    public function update(Request $request ,$id)
    {

        try {
            $com_code = Auth()->user()->com_code;
            $data = Departements::where(['com_code' => $com_code ,'id' => $id])->first();

            $validator = Validator::make( $request->all(),[
                'name' => $data != null ? 'required|min:3|unique:departements,name,'.$id : 'required|min:3',
            ]);
    
            if ($validator->fails()) {
                return $this->returnValidationError(404,$validator);
            }

            $departement  = Departements::findOrFail($id);

            $request_data = $request->except(['token']);
            if ($request->phone != null) {
                $request_data['phone'] = $request->phone;
            }elseif($request->address != null){
                $request_data['address'] = $request->address;
            }

            $departement->update($request_data);
            return $this->returnSuccessMessage('تم التعديل البيانات بنجاح');

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }   
    
    public function updateStatus($id)
    {
        try {
            $records = Departements::findOrFail($id);

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
            $records = Departements::find($id)->delete();

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
