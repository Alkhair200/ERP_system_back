<?php

namespace App\Http\Controllers\Traits;

trait GeneralTrait
{

    function saveImage($request)
    {
        $file_extension = $request->image->getClientOriginalName();

        $file_name = time().'_'.$file_extension;
        $path = $request->image->move('images/',$file_name);
        return $path;
    }

    function getSum($model,$field_name,$where = [])
    {
        $sum = $model::where($where)->sum($field_name);
        return $sum;
    }

    public function returnError($errNum = "R000", $msg)
    {
        return [
            
            'status' => false,
            'errNum' => $errNum,
            'msg' => $msg

        ];
    }

    public function returnSuccessMessage($msg = "" , $errNum = "S000")
    {
        return [
            'status' => true,
            'errNum' => $errNum,
            'msg' => $msg
        ];
    }

    public function returnData($key , $value , $msg)
    {
        return response()->json([

            'status' => true,
            'errNum' => "S000",
            'msg' => $msg,
            $key => $value
        ]);
    }  

    public function returnUserAuth($access_token , $user)
    {
        return response()->json([

            'status' => true,
            'access_token' => $access_token,
            'token_type' => 'bearer',
            'user' => $user,
        ]);
    }    

    public function returnValidationError($code = "E001" ,$validator)
    {
        return $this->returnError($code , $validator->errors());
    }

    public function returnCodeAccordingToInput($validator)
    {
        $inputs = array_keys($validator->errors()->toArray());

        $code = $this->getErrorCode($inputs[0]);
        return $code;
    }   
    
    public function getErrorCode($input)
    {
        if ($input == 'name') {
            
            return 'E0011';

        }elseif ($input == 'email') {
            
            return 'E007';

        }elseif ($input == 'password') {
            
            return 'E002';

        }elseif ($input == 'phone') {
            
            return 'E003';

        }else {
            
            return "";
        }
    }
    
    function getUserShift($model)
    {
        // اذا كان المستخدم لديه شفت مفتوح حاليا  

        $com_code = Auth()->user()->com_code;
        $admin_id = Auth()->user()->id;

        $check_exsits_open_shift = $model::
            with(['treasury' =>function($q){
                $q->select('id','name');
            }])->where([
            'com_code'=>$com_code,
            'admin_id'=>$admin_id,
            'is_finished' =>0
            ])->select('id','treasury_id','admin_id')->first();  

        return $check_exsits_open_shift;
    }
}
