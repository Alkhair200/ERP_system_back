<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SuppliersCategories;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Controllers\Traits\GeneralTrait;

class SuppliersCategoriesController extends Controller
{
    use GeneralTrait;
    
    public function index()
    {
        $com_code = Auth()->user()->com_code;
        $data = SuppliersCategories::where('com_code' , $com_code)->orderBy('id', 'desc')->get();
        return $this->returnData('suppliersCategories', $data , 'تم ارسال البيانات بنجاح');
    }

    public function store(SupplierRequest $request)
    {
        try {
            $request['com_code'] = Auth()->user()->com_code;
            $request['date'] = date('Y-m-d');
            $request['admin_id'] = Auth()->user()->id;

            SuppliersCategories::create($request->except(['token']));

            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }   
    
    public function update(SupplierUpdateRequest $request ,$id)
    {

        try {
            $data = SuppliersCategories::find($id);

            $data->update($request->except(['token']));
            return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $records = SuppliersCategories::find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function updateStatus($id)
    {
        try {
            $records = SuppliersCategories::find($id);

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
