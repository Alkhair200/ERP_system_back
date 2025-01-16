<?php

namespace App\Http\Controllers\Api;

use App\Models\Stores;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoresRequest;
use App\Http\Requests\StoresUpdateRequest;
use App\Http\Controllers\Traits\GeneralTrait;

class StoresController extends Controller
{
    use GeneralTrait;
    
    public function index()
    {
        $data = Stores::orderBy('created_at', 'desc')->get();
        return $this->returnData('stores', $data , 'تم ارسال البيانات بنجاح');
    }

    public function store(StoresRequest $request)
    {
        try {
            $request['com_code'] = Auth()->user()->com_code;
            $request['date'] = date('Y-m-d');
            $request['admin_id'] = Auth()->user()->id;

            Stores::create($request->except(['token']));
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function update(StoresUpdateRequest $request ,$id)
    {

        try {
            $data = Stores::find($id);

            $data->update($request->except(['token']));
            return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $records = Stores::find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function updateStatus($id)
    {
        try {
            $records = Stores::find($id);

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
