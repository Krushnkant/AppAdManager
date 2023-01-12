<?php

use Illuminate\Support\Facades\Route;





Route::get('frontend/logout', function() {
    if(session()->has('customer')){
        session()->pull('customer');
    }
   return redirect('/');
});


//Admin  Rpute
Route::get('admin',[\App\Http\Controllers\admin\AuthController::class,'index'])->name('admin.login');
Route::post('adminpostlogin', [\App\Http\Controllers\admin\AuthController::class, 'postLogin'])->name('admin.postlogin');
Route::get('logout', [\App\Http\Controllers\admin\AuthController::class, 'logout'])->name('admin.logout');
Route::get('admin/403_page',[\App\Http\Controllers\admin\AuthController::class,'invalid_page'])->name('admin.403_page');

Route::get('admin/403_page',[\App\Http\Controllers\admin\AuthController::class,'invalid_page'])->name('admin.403_page');

Route::group(['prefix'=>'admin','middleware'=>['auth'],'as'=>'admin.'],function () {

    Route::get('dashboard', [\App\Http\Controllers\admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('users',[\App\Http\Controllers\admin\UserController::class,'index'])->name('users.list');
    Route::post('addorupdateuser',[\App\Http\Controllers\admin\UserController::class,'addorupdateuser'])->name('users.addorupdate');
    Route::post('alluserslist',[\App\Http\Controllers\admin\UserController::class,'alluserslist'])->name('alluserslist');
    Route::get('changeuserstatus/{id}',[\App\Http\Controllers\admin\UserController::class,'changeuserstatus'])->name('users.changeuserstatus');

    Route::get('pricerange/{id}',[\App\Http\Controllers\admin\PriceRangeController::class,'index'])->name('pricerange.list');
    Route::post('addorupdatepricerange',[\App\Http\Controllers\admin\PriceRangeController::class,'addorupdatepricerange'])->name('pricerange.addorupdate');
    Route::post('allpricerangeslist',[\App\Http\Controllers\admin\PriceRangeController::class,'allpricerangeslist'])->name('allpricerangeslist');
    Route::get('changepricerangestatus/{id}',[\App\Http\Controllers\admin\PriceRangeController::class,'changepricerangestatus'])->name('pricerange.changepricerangestatus');
    Route::get('pricerange/{id}/edit',[\App\Http\Controllers\admin\PriceRangeController::class,'editpricerange'])->name('pricerange.edit');
    Route::get('pricerange/{id}/delete',[\App\Http\Controllers\admin\PriceRangeController::class,'deletepricerange'])->name('pricerange.delete');


    Route::get('purchase',[\App\Http\Controllers\admin\PurchaseController::class,'index'])->name('purchase.list');
    Route::post('allpurchaseslist',[\App\Http\Controllers\admin\PurchaseController::class,'allpurchaseslist'])->name('allpurchaseslist');

    Route::get('contactmessage',[\App\Http\Controllers\admin\ContactMessageController::class,'index'])->name('contactmessage.list');
    Route::post('allcontactmessageslist',[\App\Http\Controllers\admin\ContactMessageController::class,'allcontactmessageslist'])->name('allcontactmessageslist');

    Route::get('notifications',[\App\Http\Controllers\admin\NotificationController::class,'index'])->name('notifications.list');
    Route::get('notifications/create',[\App\Http\Controllers\admin\NotificationController::class,'create'])->name('notifications.add');
    Route::post('notifications/uploadfile',[\App\Http\Controllers\admin\NotificationController::class,'uploadfile'])->name('notifications.uploadfile');
    Route::post('notifications/removefile',[\App\Http\Controllers\admin\NotificationController::class,'removefile'])->name('notifications.removefile');
    Route::post('notifications/save',[\App\Http\Controllers\admin\NotificationController::class,'save'])->name('notifications.save');
    Route::post('allnotificationlist',[\App\Http\Controllers\admin\NotificationController::class,'allnotificationlist'])->name('allnotificationlist');
    Route::get('notifications/{id}/send',[\App\Http\Controllers\admin\NotificationController::class,'sendnotification'])->name('notifications.send');
   
    
    Route::get('applications',[\App\Http\Controllers\admin\AppController::class,'index'])->name('applications.list');
    Route::post('allappslist',[\App\Http\Controllers\admin\AppController::class,'allappslist'])->name('allappslist');
    Route::get('appupdate/{id?}',[\App\Http\Controllers\admin\AppController::class,'createAppPageData'])->name('applications.create');
    Route::get('adsReport/{id}',[\App\Http\Controllers\admin\AppController::class,'adsReportData'])->name('applications.adsReport');
    Route::post('adsReportAllData',[\App\Http\Controllers\admin\AppController::class,'adsReportAllData'])->name('applications.adsReportAllData');
    Route::post('appupdate/updateAppData',[\App\Http\Controllers\admin\AppController::class,'updateAppData'])->name('appupdate.updateAppData');
    Route::post('appupdate/uploadfile',[\App\Http\Controllers\admin\AppController::class,'uploadfile'])->name('appupdate.uploadfile');
    Route::post('appupdate/removefile',[\App\Http\Controllers\admin\AppController::class,'removefile'])->name('appupdate.removefile');
    Route::get('changeAppStatus/{id}',[\App\Http\Controllers\admin\AppController::class,'changeAppStatus'])->name('applications.changeAppStatus');
    Route::get('applications/{id}/delete',[\App\Http\Controllers\admin\AppController::class,'deleteApp'])->name('applications.delete');
    Route::post('categories/uploadfile',[\App\Http\Controllers\admin\CategoryController::class,'uploadfile'])->name('categories.uploadfile');
    Route::get('appupdate/images/appThumb/{filename}', function($filename){
        $path = public_path('images/appThumb/'.$filename);
    
        if(!File::exists($path)) {
            return response()->json(['message' => 'Image not found.'], 404);
        }
    
        $file = File::get($path);
        $type = File::mimeType($path);
        $fileSize = File::size($path);

        $imgProperties = array(
                            "fileSize" => $fileSize,
                            "mimeType" => $type,
                        );

        // $response = Response::make($file, 200);
        // $response->header("Content-Type", $type);
        
        return json_encode($imgProperties);
    });

});

Route::group(['middleware'=>['auth']],function (){
    Route::get('profile',[\App\Http\Controllers\admin\ProfileController::class,'profile'])->name('profile');
    Route::get('profile/{id}/edit',[\App\Http\Controllers\admin\ProfileController::class,'edit'])->name('profile.edit');
    Route::post('profile/update',[\App\Http\Controllers\admin\ProfileController::class,'update'])->name('profile.update');
    
    
});
