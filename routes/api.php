<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobsController;
use App\Http\Controllers\Api\AdminsController;
use App\Http\Controllers\Api\StoresController;
use App\Http\Controllers\Api\InvUomsController;
use App\Http\Controllers\Api\AccountsController;
use App\Http\Controllers\Api\EmplyeesController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\CustomersController;
use App\Http\Controllers\Api\SuppliersController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\TreasuriesController;
use App\Http\Controllers\Api\AcountTypesController;
use App\Http\Controllers\Api\InvItemCardController;
use App\Http\Controllers\Api\ShiftsTypesController;
use App\Http\Controllers\Api\AdminSettingController;
use App\Http\Controllers\Api\AdminsShiftsController;
use App\Http\Controllers\Api\DepartementsController;
use App\Http\Controllers\Api\SalesMatrialTypesController;
use App\Http\Controllers\Api\TreasuriesDeliveryController;
use App\Http\Controllers\Api\SuppliersCategoriesController;
use App\Http\Controllers\Api\SuppliersWithOrdersController;
use App\Http\Controllers\Api\TreasuriesTransactionsController;
use App\Http\Controllers\Api\SuppliersWithOrdersDetailsController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


define('PAGINATION_COUNT' ,15) ; 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['prefix' => 'dashboard'], function ($router) {

    Route::post('login', [App\Http\Controllers\Api\AuthController::class,'login'])->name('login');



    /////////////////  start  if authenticate  jwt ///////////////////////////
    Route::group(['middleware' => 'jwt.verify'],function(){
        

        Route::post('logout', [AuthController::class,'logout'])->name('logout');
        Route::post('refresh', [AuthController::class,'refresh'])->name('refresh');
        Route::post('me', [AuthController::class,'me'])->name('me');

        Route::controller(AdminSettingController::class)
        ->group(function(){
            route::post('setting-index', 'index');
            Route::post('setting-create', 'create');
            Route::post('setting-store', 'store');
            Route::patch('setting-update/{id}', 'update');
            Route::post('setting-destroy/{id}', 'destroy');
        });

        Route::controller(AdminsController::class)
        ->group(function(){
            route::post('admin-index', 'index');
            Route::post('admin-create', 'create');
            Route::post('admin-store', 'store');
            Route::patch('admin-update/{id}', 'update');
            Route::post('admin-details/{id}', 'details');
            Route::post('admin-destroy/{id}', 'destroy');
            Route::post('admin-destroy-treasury/{id}', 'adminDestroyTreasury');
            Route::post('admin-update-status/{id}', 'updateStatus');
            
            Route::post('add-treasury-to-admin', 'addTreasuryToAdmin');
        });        


        Route::controller(TreasuriesController::class)
        ->group(function(){
            route::post('treasuries-index', 'index');
            Route::post('treasuries-store', 'store');
            Route::patch('treasuries-update/{id}', 'update');
            Route::post('treasuries-destroy/{id}', 'destroy');
            Route::post('treasuries-status/{id}', 'updateStatus');
            Route::post('treasuries-details/{id}', 'details');
            
        });       

        Route::controller(TreasuriesDeliveryController::class)
        ->group(function(){
            Route::post('treasuries-delivery-store', 'store');
            Route::patch('treasuries-delivery-update/{id}', 'update');
            Route::post('treasuries-delivery-destroy/{id}', 'destroy');
            
        });

        Route::controller(SalesMatrialTypesController::class)
        ->group(function(){
            Route::post('sales-matrial-index', 'index');
            Route::post('sales-matrial-store', 'store');
            Route::patch('sales-matrial-update/{id}', 'update');
            Route::post('sales-matrial-destroy/{id}', 'destroy');
            Route::post('sales-matrial-status/{id}', 'updateStatus');
        });  
        
        Route::controller(StoresController::class)
        ->group(function(){
            Route::post('stores-index', 'index');
            Route::post('stores-store', 'store');
            Route::patch('stores-update/{id}', 'update');
            Route::post('stores-destroy/{id}', 'destroy');
            Route::post('stores-status/{id}', 'updateStatus');
        });       
        
        Route::controller(InvUomsController::class)
        ->group(function(){
            Route::post('uoms-index', 'index');
            Route::post('uoms-store', 'store');
            Route::patch('uoms-update/{id}', 'update');
            Route::post('uoms-destroy/{id}', 'destroy');
            Route::post('uoms-status/{id}', 'updateStatus');
        });  

        Route::controller(CategoriesController::class)
        ->group(function(){
            Route::post('categories-index', 'index');
            Route::post('categories-store', 'store');
            Route::post('categories-update', 'update');
            Route::post('categories-destroy/{id}', 'destroy');
            Route::post('categories-status/{id}', 'updateStatus');
        });         
        
        Route::controller(InvItemCardController::class)
        ->group(function(){
            Route::post('item-card-index', 'index');
            Route::post('item-card-store', 'store');
            Route::post('item-card-edit/{id}', 'edit');
            Route::post('item-card-show/{id}', 'show');
            Route::post('item-card-update/{id}', 'update');
            Route::post('item-card-destroy/{id}', 'destroy');
            Route::post('item-card-status/{id}', 'updateStatus');
        });    
        
        Route::controller(AcountTypesController::class)
        ->group(function(){
            Route::post('account-types-index', 'index');
            Route::post('account-types-store', 'store');
            Route::post('account-types-edit/{id}', 'edit');
            Route::post('account-types-update/{id}', 'update');
            Route::post('account-types-destroy/{id}', 'destroy');
            Route::post('account-types-status/{id}', 'updateStatus');
        });  


        Route::controller(AccountsController::class)
        ->group(function(){
            Route::post('account-index', 'index');
            Route::post('account-create', 'create');
            Route::post('account-store', 'store');
            Route::post('account-edit/{id}', 'edit');
            Route::post('account-update/{id}', 'update');
            Route::post('account-destroy/{id}', 'destroy');
            Route::post('account-status/{id}', 'updateStatus');
        });  

        // العملاء
        Route::controller(CustomersController::class)
        ->group(function(){
            Route::post('customer-index', 'index');
            Route::post('customer-create', 'create');
            Route::post('customer-store', 'store');
            Route::post('customer-edit/{id}', 'edit');
            Route::post('customer-update/{id}', 'update');
            Route::post('customer-destroy/{id}', 'destroy');
            Route::post('customer-status/{id}', 'updateStatus');
        });   

        // الموردين
        Route::controller(SuppliersController::class)
        ->group(function(){
            Route::post('supplier-index', 'index');
            Route::post('supplier-create', 'create');
            Route::post('supplier-store', 'store');
            Route::post('supplier-edit/{id}', 'edit');
            Route::post('supplier-update/{id}', 'update');
            Route::post('supplier-destroy/{id}', 'destroy');
            Route::post('supplier-status/{id}', 'updateStatus');
        });        
        
        
        Route::controller(SuppliersCategoriesController::class)
        ->group(function(){
            Route::post('supplier-cat-index', 'index');
            Route::post('supplier-cat-store', 'store');
            Route::patch('supplier-cat-update/{id}', 'update');
            Route::post('supplier-cat-destroy/{id}', 'destroy');
            Route::post('supplier-cat-status/{id}', 'updateStatus');
        });   
        
        // فواتير مشتريات 
        Route::controller(SuppliersWithOrdersController::class)
        ->group(function(){
            Route::post('supplier-order-index', 'index');
            Route::post('supplier-order-create', 'create');
            Route::post('supplier-order-store', 'store');
            Route::patch('supplier-order-edit/{id}', 'update');
            Route::patch('supplier-order-update/{id}', 'update');
            Route::post('supplier-order-destroy/{id}', 'destroy');
            Route::post('supplier-order-do-approved', 'DoApproved');
            // عتماد الفاتورة
            Route::post('supplier-order-approve-invoice', 'approveInvoice');

            Route::post('supplier-order-details', 'details');
            Route::post('supplier-get-uoms', 'getUoms');
            Route::post('supplier-add-new-details', 'newDetails');
            Route::patch('supplier-update-details/{id}', 'updateDetails');
        });      
        
        // اصناف فواتير المشتريات 
        Route::controller(SuppliersWithOrdersDetailsController::class)
        ->group(function(){
            Route::post('supplier-details-destroy/{id}', 'destroy');
        });         
        
        // Admins shifts  شفتات الخزن
        Route::controller(AdminsShiftsController::class)
        ->group(function(){
            Route::post('admin-shift-index', 'index');
            Route::post('admin-shift-create', 'create');
            Route::post('admin-shift-store', 'store');
            Route::patch('admin-shift-edit/{id}', 'update');
            Route::patch('admin-shift-update/{id}', 'update');
            Route::post('admin-shift-destroy/{id}', 'destroy');
        });   
        
        // Treasuries Transactions حركة الخزن بالشفتات
        Route::controller(TreasuriesTransactionsController::class)
        ->group(function(){
            Route::post('treasuries-transac-index', 'index');
            Route::post('treasuries-transac-store', 'store');
            Route::patch('treasuries-transac-edit/{id}', 'update');
            Route::patch('treasuries-transac-update/{id}', 'update');
            Route::post('treasuries-transac-destroy/{id}', 'destroy');
        });   

        // صرف النقديه
        Route::controller(ExchangeController::class)
        ->group(function(){
            Route::post('exchange-index', 'index');
            Route::post('exchange-store', 'store');
            Route::patch('exchange-edit/{id}', 'update');
            Route::patch('exchange-update/{id}', 'update');
            Route::post('exchange-destroy/{id}', 'destroy');
        });          
        
        // إدارات المنشأة
        Route::controller(DepartementsController::class)
        ->group(function(){
            Route::post('departements-index', 'index');
            Route::post('departements-store', 'store');
            Route::patch('departements-edit/{id}', 'update');
            Route::patch('departements-update/{id}', 'update');
            Route::post('departements-destroy/{id}', 'destroy');
            Route::post('departements-status/{id}', 'updateStatus');
        });  
        
        // انواع الوظائف
        Route::controller(JobsController::class)
        ->group(function(){
            Route::post('jobs-index', 'index');
            Route::post('jobs-store', 'store');
            Route::patch('jobs-edit/{id}', 'update');
            Route::patch('jobs-update/{id}', 'update');
            Route::post('jobs-destroy/{id}', 'destroy');
            Route::post('jobs-status/{id}', 'updateStatus');
        });    
        
        // انواع الشفتات
        Route::controller(ShiftsTypesController::class)
        ->group(function(){
            Route::post('shifts-index', 'index');
            Route::post('shifts-store', 'store');
            Route::patch('shifts-edit/{id}', 'edit');
            Route::patch('shifts-update/{id}', 'update');
            Route::post('shifts-destroy/{id}', 'destroy');
            Route::post('shifts-status/{id}', 'updateStatus');
        });    
        
        // الموظفين
        Route::controller(EmplyeesController::class)
        ->group(function(){
            Route::post('employee-index', 'index');
            Route::post('employee-store', 'store');
            Route::post('employee-create', 'create');
            Route::post('employee-search', 'search');
            Route::patch('employee-edit/{id}', 'edit');
            Route::patch('employee-update/{id}', 'update');
            Route::post('employee-destroy/{id}', 'destroy');
            Route::post('employee-status/{id}', 'updateStatus');
        });           
        
        /////////////////  end   if authenticate  jwt ///////////////////////////


    });






});

// Route::group(['namespace'=>'admin','prefix'=>'admin'] ,function(){
//     route::get('/test',function ()
//     {
//         return "test route";
//     })->name('test');
// });
