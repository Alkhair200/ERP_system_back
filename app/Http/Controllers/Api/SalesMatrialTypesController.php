<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Sales_Matrial_types;
use App\Http\Controllers\Controller;
use App\Http\Requests\SalesMatrialRequest;
use App\Http\Controllers\Traits\GeneralTrait;
use App\Http\Requests\SalesMatrialUpdateRequest;


class SalesMatrialTypesController extends Controller
{
    use GeneralTrait;
    
    public function index()
    {
        $data = Sales_Matrial_types::orderBy('created_at', 'desc')->get();
        return $this->returnData('salesMatrialTypes', $data , 'تم ارسال البيانات بنجاح');
    }

    public function store(SalesMatrialRequest $request)
    {
        try {
            $request['com_code'] = Auth()->user()->com_code;
            $request['date'] = date('Y-m-d');
            $request['admin_id'] = Auth()->user()->id;

            Sales_Matrial_types::create($request->except(['token']));
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function update(SalesMatrialUpdateRequest $request ,$id)
    {

        try {
            $data = Sales_Matrial_types::find($id);

            $data->update($request->except(['token']));
            return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $records = Sales_Matrial_types::find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function updateStatus($id)
    {
        try {
            $records = Sales_Matrial_types::find($id);

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
