<?php

namespace App\Http\Controllers\Api;

use App\Models\InvUoms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvUomsRequest;
use App\Http\Requests\InvUomsUpdateRequest;
use App\Http\Controllers\Traits\GeneralTrait;

class InvUomsController extends Controller
{
    use GeneralTrait;
    
    public function index(Request $request)
    {   
        $data = InvUoms::when($request->search ,function($qr) use ($request){
            
            if (is_numeric($request->search)) {

                return $qr->where('is_master' ,'like','%' .$request->search. '%');

            } else {
                return $qr->where('name' ,'like','%' .$request->search. '%');
            }
            
        })->orderBy('created_at', 'desc')->get();
   
        return $this->returnData('invUoms', $data , 'تم ارسال البيانات بنجاح');
    }

    public function store(InvUomsRequest $request)
    {
        
        try {
            $request['com_code'] = Auth()->user()->com_code;
            $request['date'] = date('Y-m-d');
            $request['admin_id'] = Auth()->user()->id;

            InvUoms::create($request->except(['token']));
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function update(InvUomsUpdateRequest $request ,$id)
    {

        try {
            $data = InvUoms::find($id);
            $data->update($request->except(['token']));
            return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $records = InvUoms::find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function updateStatus($id)
    {
        try {
            $records = InvUoms::find($id);

            if ($records->active == 'مفعل') {
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
