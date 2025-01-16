<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FormsController extends Controller
{
    public function save(Request $request)
    {
        $validator = Validator::make( $request->all(),[
            'name' => 'required',
            'nickname' => 'required',
            'email' => 'required|email',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(404,$validator);
        }
    }
}
