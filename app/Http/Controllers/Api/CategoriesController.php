<?php

namespace App\Http\Controllers\Api;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriesRequest;
use App\Http\Controllers\Traits\GeneralTrait;
use App\Http\Requests\updateCategoriesRequest;

class CategoriesController extends Controller
{
    use GeneralTrait;
    
    public function index(Request$request)
    {
        $data = Categories::when($request->search, function($q) use($request){

            return $q->where('name' , 'like' , '%' .$request->search. '%');

        })->orderBy('updated_at', 'desc')->paginate(PAGINATION_COUNT);
        return $this->returnData('categories', $data , 'تم ارسال البيانات بنجاح');
    }

    public function store(CategoriesRequest $request)
    {
        // try {

            $request['com_code'] = Auth()->user()->com_code;
            $request['admin_id'] = Auth()->user()->id;

            Categories::create($request->except(['token']));
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        // } catch (\Throwable $ex) {
        //     return $this->returnError(404,$ex->getMessage());
        // }
    }

    public function update(updateCategoriesRequest $request)
    {

        try {
            $data = Categories::find($request->id);

            $data->update($request->except(['token']));
            return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $records = Categories::find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function updateStatus($id)
    {
        try {
            $records = Categories::find($id);

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
