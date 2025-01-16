<?php

namespace App\Http\Controllers\Api;

use DB;
use Validator;
use App\Models\Accounts;
use App\Models\Exchange;
use App\Models\MoveType;
use App\Models\Treasuries;
use Illuminate\Http\Request;
use App\Models\Admins_shifts;
use App\Http\Controllers\Controller;
use App\Models\TreasuriesTransactions;
use App\Http\Controllers\Traits\GeneralTrait;

class ExchangeController extends Controller
{
    use GeneralTrait;
    
    public function index()
    {
        $com_code = Auth()->user()->com_code;
        $admin_id = Auth()->user()->id;

        $data = TreasuriesTransactions::with(['treasury' =>function($q){
            $q->select('id','name');
            
        },'admin'=>function($q){
            $q->select('id','name');
        },'moveType'=>function($q){
            $q->select('id','name');
        },'adminShift'])->where('com_code',$com_code)->where('money' ,'<', 0)
        ->orderBy('updated_at', 'desc')
        ->paginate(PAGINATION_COUNT);

        // اذا كان المستخدم لديه شفت مفتوح حاليا  
        $check_exsits_open_shift = Admins_shifts::
            with(['treasury' =>function($q){
                $q->select('id','name');
            }])->where([
            'com_code'=>$com_code,
            'admin_id'=>$admin_id,
            'is_finished' =>0
            ])->select('id','treasury_id','admin_id')->first();   

            $accounts = Accounts::with('accountType')->where([
                'com_code'=>$com_code,
                'is_archived'=>0,
                'is_parent'=>2
                ])->orderBy('updated_at','desc')->get(); 

            // 1- صرف نقديه    
            $move_types = MoveType::where([
                'active'=>1,'in_screen'=>1,
                'is_private_internal'=>0
                ])->select('id','name')->get();              
            
        if ($check_exsits_open_shift != null) {
            // Get treasuries balance now
            $check_exsits_open_shift['treasury_balance_now'] = 
            $this->getSum(new TreasuriesTransactions,'money',[
                'com_code'=>$com_code,
                'admin_shift_id'=>$check_exsits_open_shift->id
            ]);        
        }
                
        return response()->json([
            'status' => true,
            'errNum' => "S000",
            'masg'=> 'تم ارسال البيانات بنجاح',
            'exchanges'=> $data,
            'check_exsits_open_shift' =>$check_exsits_open_shift,
            'accounts' =>$accounts,
            'move_types' =>$move_types,
        ]); 

    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $admin_id = Auth()->user()->id;
        $com_code = Auth()->user()->com_code;

        $validator = Validator::make( $request->all(),[
            'treasury_id' => 'required',
            'date' => 'required',
            'move_type_id' => 'required',
            'account_num' => 'required',
            'money' => 'required',
            'byan' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }         

        try {   

            DB::beginTransaction();     

            // check if user hase open shift or not
            $check_exsits_open_shift = Admins_shifts::where([
                'com_code'=>$com_code,
                'admin_id'=>$admin_id,
                'treasury_id'=> $request->treasury_id,
                'is_finished' =>0
                ])->first();   
                

            if ($check_exsits_open_shift == null) {
                return response()->json([
                    'status' => false,
                    'errNum' => 'R001',
                    'errors' => 'لا يوجد شفت خزنة مفتوح حاليا'
                ]);
            }             
                
            // get isal number with treasury
            $treasury = Treasuries::where([
                'com_code'=>$com_code ,
                'id'=>$request->treasury_id
                ])->select('id','last_isal_exchange')->orderBy('id','desc')->first();

            $isal_num = 0;
            if ($treasury == null) {
                if ($check_exsits_open_shift == null) {
                    return response()->json([
                        'status' => false,
                        'errNum' => 'R001',
                        'errors' => 'الخزنه المختاره غير موجوده'
                    ]);
                }               
            } else{
                $isal_num = $treasury->last_isal_exchange +1;
            } 

            // set order serial number
            $get_auto_serial = TreasuriesTransactions::where('com_code' , $com_code)
            ->select('auto_serial')->orderBy('id','desc')->first();

            if ($get_auto_serial != null) {
                $auto_serial = $get_auto_serial->auto_serial +1;
            }else{
                $auto_serial = 1;
            }         

            TreasuriesTransactions::create([
                'auto_serial' => $auto_serial,
                'admin_id' => $admin_id,
                'isal_num' => $isal_num,
                'com_code' => $com_code,
                'admin_shift_id' => $check_exsits_open_shift->id,
                // Credit دائن
                'money' =>$request->money*(-1),
                'treasury_id' =>$request->treasury_id,
                'move_type_id' =>$request->move_type_id,
                'date' =>$request->date,
                'account_num' =>$request->account_num,
                'is_account' =>1, // صرف من حساب مالي
                'is_approved' =>1,
                'money_for_account' => $request->money, // Debit مدين 
                'byan' =>$request->byan,
            ]);      

            
            $treasuryUpdateLastIsalCollect = Treasuries::where([
                'com_code'=>$com_code ,
                'id'=>$request->treasury_id
                ])->first();

            $treasuryUpdateLastIsalCollect->update(['last_isal_exchange' => $isal_num]);

            DB::commit();

            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');   

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }           
    }

    public function show(Exchange $exchange)
    {
        //
    }

    public function edit(Exchange $exchange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exchange $exchange)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exchange $exchange)
    {
        //
    }
}
