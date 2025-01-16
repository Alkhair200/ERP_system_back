<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Models\Treasuries;
use Illuminate\Http\Request;
use App\Models\Treasuries_Delivery;
use App\Http\Controllers\Controller;
use App\Http\Requests\TreasuriesRequest;
use App\Http\Controllers\Traits\GeneralTrait;
use App\Http\Requests\TreasuriesUpdateRequest;

class TreasuriesController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }


    public function index(Request $request)
    {
        $com_code = Auth()->user()->com_code;

        $records = Treasuries::when($request->search, function($query) use($request){

            return $query->where('name' , 'like' , '%' .$request->search. '%');

        })->where('com_code',$com_code)
        ->orderBy('updated_at', 'desc')
        ->paginate(PAGINATION_COUNT);

        if ($request->search != null) {
            $data_count = Treasuries::where('name' , 'like' , '%' .$request->search. '%')->count();
        }else{
            $data_count = Treasuries::count();
        }

        // $records = Treasuries::orderBy('created_at', 'desc')->where('com_code',$com_code)->paginate(PAGINATION_COUNT);
        
        $data =['records'=>$records,'data_count'=>$data_count];

        return $this->returnData('treasuries', $data , 'تم ارسال البيانات بنجاح');
    }


    public function store(TreasuriesRequest $request)
    {

        try {
            $request['com_code'] = Auth()->user()->com_code;
            $request['date'] = date('Y-m-d');
            $request['admin_id'] = Auth()->user()->id;

            Treasuries::create($request->except(['token']));
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }

    }


    public function update(TreasuriesUpdateRequest $request ,$id)
    {
        try {
            $data = Treasuries::find($id);
            if (!empty($data)) {
                $request['admin_id'] = Auth()->user()->id;
                $data->update($request->except(['token']));
                return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');
            }

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }

    }

    public function updateStatus($id)
    {
        try {
            $records = Treasuries::find($id);

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

    public function destroy($id)
    {
        try {
            $records = Treasuries::find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    public function details($id)
    {
        $treasuries = Treasuries::find($id);
        
        $com_code = Auth()->user()->com_code;

        // for select option
        $all_treasuries = Treasuries::select('id','name')
        ->where('active' ,1)
        ->where('com_code' ,$com_code)
        ->get();

        $data = Treasuries_Delivery::where('treasury_id',$id)
        ->orderBy('created_at','desc')->get();

        $data = collect($data );

        $treasuriesDelivery = $data->transform(function($value,$key){
            $val =  Treasuries::where('id',$value->treasuries_can_delivery_id)->first();
            $value['name'] = $val->name;
            $value['added_by'] = Admin::find($value->admin_id)->name;


            return $value;
        });

        return response()->json([
            'status' => true,
            'masg'=> 'تم ارسال البيانات بنجاح',
            'treasuries'=> $treasuries,
            'treasuriesDelivery'=> $treasuriesDelivery,
            'all_treasuries'=> $all_treasuries,
            
         ]);
    }
}
