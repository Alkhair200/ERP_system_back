<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\InvUoms;
use App\Models\Categories;
use App\Models\InvItemCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvItemCardRequest;
use App\Http\Controllers\Traits\GeneralTrait;
use App\Http\Requests\InvItemCardEditRequest;

class InvItemCardController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }
    
    public function index(Request $request)
    {
        
        try {

            $com_code = Auth()->user()->com_code;
            $data = InvItemCard::with(['category','invUom','retalUom'])
            ->when($request->search, function($query) use($request){
                return $query->where('name' , 'like' , '%' .$request->search. '%');
    
            })->where('com_code',$com_code)
            ->orderBy('updated_at', 'desc')
            ->paginate(PAGINATION_COUNT);
    
            if ($data->count() >= 1) {
    
                foreach ($data as $key => $value) {
                    $parentName = null;
                    // if ($value->parent_inv_itemCard_id != null) {
                        $parentName = InvItemCard::where('id',$value->parent_inv_itemCard_id)
                        ->select('name')
                        ->first();
                    // }
                    
                    if($parentName != null){
                        $value['parent_item_name'] = $parentName->name;
                    }

                    if ($value->item_type == 1) {
                        $value->item_type  = 'مخزنى';
                     } elseif($value->item_type == 2){
                         $value->item_type = 'استهلاكي';
                     }elseif($value->item_type == 3){
                         $value->item_type  = 'عهدة';
                     }                      
                }
            }
    
            $categories = Categories::where(['active'=>1,'com_code'=>$com_code])
            ->select('id','name')
            ->orderBy('updated_at', 'desc')->get();
    
            $invUomsParent = InvUoms::where(['active'=>1,'is_master'=>1,'com_code'=>$com_code])
            ->select('id','name')
            ->orderBy('updated_at', 'desc')->get();
    
            $invUomsChild = InvUoms::where(['active'=>1,'is_master'=>0,'com_code'=>$com_code])
            ->select('id','name')
            ->orderBy('updated_at', 'desc')->get();   
            
            $itemCardData = InvItemCard::where(['active'=>1,'com_code'=>$com_code])
            ->select('id','name')
            ->orderBy('updated_at', 'desc')->get();        
    
            return response()->json([
                'status' => true,
                'masg'=> 'تم ارسال البيانات بنجاح',
                'invItemcards'=> $data,
                'categories'=> $categories,
                'inv_uoms_parent'=> $invUomsParent,
                'inv_uoms_child'=> $invUomsChild,
                'item_card_data' =>$itemCardData,
                
             ]);      

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }

    }  // end index


// InvItemCardRequest
    public function store(InvItemCardRequest $request)
    {    
        // try {
            
            $requestData = [];

            $request['com_code'] = Auth()->user()->com_code;
    
            if ($request->does_has_reta_unit == 1) {
                $validator = Validator::make( $request->all(),[
                    'retal_uom_id' => 'required',
                    'retal_qt_to_parent' => 'required',
    
                    // اسعار الوحدة الابن غير اساسي
                    'price_retal' => 'required',
                    'nos_gomla_price_retal' =>'required',
                    'gomla_price_retal' => 'required',
                    'cost_price_retal' => 'required',  
                    'retal_qt_to_parent' => 'required',  
                    ]);
        
                if ($validator->fails()) {
                    return $this->returnValidationError(404,$validator);
                }
            }
    
    
            $getItemcode = InvItemCard::where('com_code' ,$request->com_code)
            ->select('item_code')->latest()->first();
    
            if ($getItemcode) {
                $requestData['item_code'] = $getItemcode->item_code +1;
            }else{
                $requestData['item_code'] = 1;
            }
    
            if ($request->barcode != null) {
    
                $checkExists_barcode = InvItemCard::where(['barcode' =>$request->barcode , 'com_code' => $request->com_code])->get();
                
                if ($checkExists_barcode->count() >= 1) {
                    return response()->json([
                        'status' => false,
                        'errNum' => 'R000',
                        'errors' => ['barcode'=> ['باركود الصنف مسجل من قبل']]
                     ]);
                     
                }else{
                    $requestData['barcode'] = $request->barcode;
                }
    
            }else{
                $requestData['barcode'] = 'code'.'_'.$requestData['item_code'];
            }
    
    
            $checkExists_name = InvItemCard::where(['name' =>$request->name , 'com_code' => $request->com_code])->first();
            if (!empty($checkExists_name)) {
                return response()->json([
                    'status' => false,
                    'errNum' => 'R000',
                    'errors' => ['اسم الصنف مسجل من قبل']
                    
                 ]);
            }        
    
            // هل للصنف وحدة تجزئه ابن
            if ($request->does_has_reta_unit == 1) {
                $requestData['does_has_reta_unit'] = $request->does_has_reta_unit;
               
                $requestData['retal_uom_id'] = $request->retal_uom_id;
                $requestData['retal_qt_to_parent'] = $request->retal_qt_to_parent;
                $requestData['price_retal'] = $request->price_retal;
                $requestData['nos_gomla_price_retal'] = $request->nos_gomla_price_retal;
                $requestData['gomla_price_retal'] = $request->gomla_price_retal;
                $requestData['cost_price_retal'] = $request->cost_price_retal;
            }else{
                $requestData['does_has_reta_unit'] = $request->does_has_reta_unit;
               
                $requestData['retal_uom_id'] = null;
                $requestData['retal_qt_to_parent'] = null;
                $requestData['price_retal'] = null;
                $requestData['nos_gomla_price_retal'] = null;
                $requestData['gomla_price_retal'] = null;
                $requestData['cost_price_retal'] = null;            
            }
    
            // dd($request->parent_inv_itemCard_id);
            $requestData['name'] = $request->name;
            $requestData['item_type'] = $request->item_type;
            $requestData['category_id'] = $request->category_id;
            $requestData['parent_inv_itemCard_id'] = $request->parent_inv_itemCard_id;
    
            // if ($requestData['parent_inv_itemCard_id'] == '') {
            //     $requestData['parent_inv_itemCard_id'] = 0;
            // }
            
            $requestData['uom_id'] = $request->uom_id;
            
            // اسعار الوحدة الاب اساسي
            $requestData['price'] = $request->price;
            $requestData['nos_gomla_price'] = $request->nos_gomla_price;
            $requestData['gomla_price'] = $request->gomla_price;
            $requestData['post_price'] = $request->post_price;
            
            $requestData['has_fixced_price'] = $request->has_fixced_price;
            $requestData['active'] = $request->active;
            $requestData['image'] = $request->image;
            $requestData['com_code'] = $request->com_code;
            $requestData['admin_id'] = Auth()->user()->id;
            
            $requestData['date'] = date('Y-m-d');  
            
            if ($request->image != null) {
    
                $path = $this->saveImage($request);
                $requestData['image'] = $path;
            }            
    
            InvItemCard::create($requestData);
            return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');  

        // } catch (\Throwable $ex) {
        //     return $this->returnError(404,$ex->getMessage());
        // }
    }

    public function edit(Request $request, $id)
    {

        $com_code = Auth()->user()->com_code;

        $data = InvItemCard::with(['category','invUom','retalUom'])->findOrFail($id);    
        
        $categories = Categories::where(['active'=>1,'com_code'=>$com_code])
        ->select('id','name')
        ->orderBy('updated_at', 'desc')->get();

        $invUomsParent = InvUoms::where(['active'=>1,'is_master'=>1,'com_code'=>$com_code])
        ->select('id','name')
        ->orderBy('updated_at', 'desc')->get();

        $invUomsChild = InvUoms::where(['active'=>1,'is_master'=>0,'com_code'=>$com_code])
        ->select('id','name')
        ->orderBy('updated_at', 'desc')->get();   
        
        $itemCardData = InvItemCard::where(['active'=>1,'com_code'=>$com_code])
        ->select('id','name')
        ->orderBy('updated_at', 'desc')->get();    

        return response()->json([
            'status' => true,
            'masg'=> 'تم ارسال البيانات بنجاح',
            'invItemcard'=> $data,
            'categories'=> $categories,
            'inv_uoms_parent'=> $invUomsParent,
            'inv_uoms_child'=> $invUomsChild,
            'item_card_data' =>$itemCardData,
         ]);  
    }    

    public function show( $id)
    {
        $data = InvItemCard::with(['category','invUom','retalUom'])->findOrFail($id); 

        if ($data != '') {
            if ($data->item_type == 1) {
               $data->item_type  = 'مخزنى';
            } elseif($data->item_type == 2){
                $data->item_type = 'استهلاكي';
            }elseif($data->item_type == 3){
                $data->item_type  = 'عهدة';
            }  

            if ($data->does_has_reta_unit == 1) {
                $data->does_has_reta_unit = 'نعم';
            }else{
                $data->does_has_reta_unit = 'لا';
            }

            $parentName = null;
            // if ($value->parent_inv_itemCard_id != null) {
                $parentName = InvItemCard::where('id',$data->parent_inv_itemCard_id)
                ->select('name')
                ->first();
            // }
            
            if($parentName != null){
                $data['parent_item_name'] = $parentName->name;
            }            
            
        }

      
        return $this->returnData('invItemcard', $data , 'تم ارسال البيانات بنجاح');
    }


    // InvItemCardEditRequest
    public function update(InvItemCardEditRequest $request, $id)
    {
        try {
  
        $data = InvItemCard::find($id);    
        
        $requestData = [];

        $com_code = Auth()->user()->com_code;

        if ($request->does_has_reta_unit == 1) {
            $validator = Validator::make( $request->all(),[
                'retal_uom_id' => 'required',
                'retal_qt_to_parent' => 'required',

                // اسعار الوحدة الابن غير اساسي
                'price_retal' => 'required',
                'nos_gomla_price_retal' =>'required',
                'gomla_price_retal' => 'required',
                'cost_price_retal' => 'required',  
                'retal_qt_to_parent' => 'required',  
                ]);
    
                if ($validator->fails()) {
                    return $this->returnValidationError(404,$validator);
                }
        }


        $getItemcode = InvItemCard::where('com_code' ,$com_code)
        ->select('item_code')->latest()->first();

        if ($getItemcode) {
            $requestData['item_code'] = $getItemcode->item_code +1;
        }else{
            $requestData['item_code'] = 1;
        }

        if ($request->barcode != null) {

           $checkExists_barcode = InvItemCard::where('id','!=',$id)
           ->where(['com_code' => $com_code ,'barcode' => $request->barcode])->get();
            
            if ($checkExists_barcode->count() >= 1) {
                return response()->json([
                    'status' => false,
                    'errNum' => 'R000',
                    'errors' => ['barcode'=> ['باركود الصنف مسجل من قبل']]
                    
                 ]);
            }else{
                $requestData['barcode'] = $request->barcode;
            }

        }else{
            $requestData['barcode'] = 'code'.'_'.$requestData['item_code'];
        }

        // هل للصنف وحدة تجزئه ابن
        if ($request->does_has_reta_unit == 1) {
            $requestData['does_has_reta_unit'] = $request->does_has_reta_unit;
           
            $requestData['retal_uom_id'] = $request->retal_uom_id;
            $requestData['retal_qt_to_parent'] = $request->retal_qt_to_parent;
            $requestData['price_retal'] = $request->price_retal;
            $requestData['nos_gomla_price_retal'] = $request->nos_gomla_price_retal;
            $requestData['gomla_price_retal'] = $request->gomla_price_retal;
            $requestData['cost_price_retal'] = $request->cost_price_retal;
        }else{
            $requestData['does_has_reta_unit'] = $request->does_has_reta_unit;
           
            $requestData['retal_uom_id'] = null;
            $requestData['retal_qt_to_parent'] = null;
            $requestData['price_retal'] = null;
            $requestData['nos_gomla_price_retal'] = null;
            $requestData['gomla_price_retal'] = null;
            $requestData['cost_price_retal'] = null;            
        }

        $requestData['name'] = $request->name;
        $requestData['item_type'] = $request->item_type;
        $requestData['category_id'] = $request->category_id;
        $requestData['parent_inv_itemCard_id'] = $request->parent_inv_itemCard_id;

        
        $requestData['uom_id'] = $request->uom_id;
        
        // اسعار الوحدة الاب اساسي
        $requestData['price'] = $request->price;
        $requestData['nos_gomla_price'] = $request->nos_gomla_price;
        $requestData['gomla_price'] = $request->gomla_price;
        $requestData['post_price'] = $request->post_price;
        
        $requestData['has_fixced_price'] = $request->has_fixced_price;
        $requestData['active'] = $request->active;
        $requestData['image'] = $request->image;
        
        if ($request->image != null) {
            
            if ($data->image != null) {
                unlink($data->image);
            }

            $path = $this->saveImage($request);
            
            $requestData['image'] = $path;

        }

        $data->update($requestData);
        return $this->returnSuccessMessage('تم تعديل البيانات بنجاح');  

    } catch (\Throwable $ex) {
        return $this->returnError(404,$ex->getMessage());
    }
    }


    public function destroy($id)
    {
        try {
            
            $data = InvItemCard::find($id);
            $data->delete();
            return $this->returnSuccessMessage('تم حذف البيانات بنجاح'); 

        } catch (\Throwable $ex) {
            return $this->returnError(404,$ex->getMessage());
        }
    }    

    public function updateStatus($id)
    {
        try {
            $records = InvItemCard::find($id);

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