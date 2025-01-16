<?php

namespace App\Http\Controllers\Api;

use App\Models\Treasuries;
use Illuminate\Http\Request;
use App\Models\Treasuries_Delivery;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GeneralTrait;
use App\Http\Requests\TreasuriesDeliveryRequest;

class TreasuriesDeliveryController extends Controller
{
    use GeneralTrait;
    
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TreasuriesDeliveryRequest $request)
    {
      
        try {
            $request['com_code'] = Auth()->user()->com_code;
            $request['admin_id'] = Auth()->user()->id;

            $data = Treasuries_Delivery::where([
                'treasury_id'=>$request->treasury_id,
                'com_code'=>$request['com_code'],
                'treasuries_can_delivery_id'=> $request->treasuries_can_delivery_id
                ])->get();
    
                if ($data->count() >= 1) {
                    return response()->json([
                
                        'status' => false,
                        'errNum' => 'R000',
                        'errors' => ['هذه الخزنة مسجله من قبل']
            
                    ]);
                }

            Treasuries_Delivery::create($request->except(['token']));
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Treasuries_Delivery $treasuries_Delivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Treasuries_Delivery $treasuries_Delivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Treasuries_Delivery $treasuries_Delivery)
    {
        //
    }


    public function destroy( $id)
    {
        try {
            $records = Treasuries_Delivery::find($id)->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }
}
