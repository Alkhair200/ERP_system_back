<?php

namespace App\Http\Controllers\Api;

use App\Models\Acount_types;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GeneralTrait;


class AcountTypesController extends Controller
{
    use GeneralTrait;

    public function index()
    {
        $data = Acount_types::orderBy('updated_at', 'desc')->paginate(PAGINATION_COUNT);
        return $this->returnData('account_types', $data , 'تم ارسال البيانات بنجاح');
    }

    public function store(Request $request)
    {
        try {

            // $request['com_code'] = Auth()->user()->com_code;
            // $request['admin_id'] = Auth()->user()->id;

            Acount_types::create($request->except(['token']));
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function update(Request $request ,$id)
    {

        try {
            $data = Acount_types::find($id);

            $data->update($request->except(['token']));
            return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function updateStatus($id)
    {
        try {
            $records = Acount_types::find($id);

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
