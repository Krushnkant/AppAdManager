<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Models\users_apps_visit;
use App\Models\app_ad_requests;
use App\Models\ad_request_status;
use App\Models\PriceRange;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AppController extends BaseController
{

    public function checkUserIsValidOrNot($id){

        $userData = User::where('id',$id)->where('estatus',1)->first();
        if ((!$userData && $id != 0) || $id == 1 || $id == 2){
            return false;
        }
        return true;
    }

    public function appData(Request $request){

        $messages = [
            'appId.required' => 'App ID is Required',
            'userId.required' => 'User ID is Required',
            'deviceId.required' => 'Device ID is Required',
        ];

        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'appId' => 'required',
            'deviceId' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $appData = Application::where('id',$request->appId)->where('estatus',1)->first();

        if (!$appData){
            return $this->sendError("This Application doesn't available", "Invalid appId", []);
        }

        $userId = $request->userId;
    
        $isValidUser = $this->checkUserIsValidOrNot($userId);
     
        if ($isValidUser == false){
            return $this->sendError("This User doesn't available", "Invalid userId", []);
        }
         
        if($userId == 0){
            $user = User::where(['device_id' => $request->deviceId,'app_id' => $request->appId])->first();
            // New User
            if(!$user){
                $user = new User();
                $user->app_id = $request->appId;
                $user->role = 3;
                $user->device_id = $request->deviceId;
                $user->device_type = $request->deviceType;
                $user->device_company = $request->deviceCompany;
                $user->device_model = $request->deviceModel;
                $user->device_os_version = $request->deviceOsVersion;
                $user->estatus = 1;
                $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                
            }
            $user->last_open_time = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->fcm_id = isset($request->fcm_id)?$request->fcm_id:"";
            $user->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->save();
            $userId = (string)$user->id;
        }

        $data = array();
        $data['userId'] =  $userId;
        $is_subscription = false;
        $purchase = Purchase::where('app_id',$request->appId)->where('user_id',$request->userId)->orderby('updated_at', 'desc')->first();
        if($purchase){
            if (Carbon::parse($purchase->end_date) < Carbon::now()) {
                $is_subscription = true;
            }
        }
        $appData->setAttribute('is_subscription', $is_subscription);
        $data['appData'] =  $appData;
        // User App Visit Log
        $userAppLog = new users_apps_visit();
        $userAppLog->user_id = $userId;
        $userAppLog->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $userAppLog->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $userAppLog->save();

        return $this->sendResponseWithData($data, "App Data Retrieved Successfully.");
    }

    public function adRequest(Request $request){

        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('Y-m-d H:i:s', time());

        $messages = [
            'userId.required' => 'User ID is Required',
            'adType.required' => 'Ad Type is Required',
            'adType.integer' => 'Ad Type should be Numeric',
            'adCurrentStatus.required' => 'Ad Current Status is Required',
            'adCurrentStatus.integer' => 'Ad Current Status should be Numeric',
            'adKey.required' => 'Ad Key is Required'
        ];

        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'adType' => 'required|integer',
            'adCurrentStatus' => 'required|integer',
            'adKey' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $isValidUser = $this->checkUserIsValidOrNot($request->userId);
        if ($isValidUser == false){
            return $this->sendError("This User doesn't available", "Invalid userId", []);
        }
        $userData = User::where('id',$request->userId)->where('estatus',1)->first();
        $appReqData = app_ad_requests::where('uniq_str_key',$request->adKey)->first();
        
        $reqId = 0;
        $secondsFromRequest = 0;
        $secondsFromLastStatus = 0;
        if(!$appReqData){

            $appAdRequest = new app_ad_requests();
            $appAdRequest->user_id = $request->userId;
            $appAdRequest->app_id = $userData->app_id;
            $appAdRequest->ad_type = $request->adType;
            $appAdRequest->uniq_str_key = $request->adKey;
            $appAdRequest->request_time = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $appAdRequest->ad_current_status = $request->adCurrentStatus;
            $appAdRequest->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            
            $appAdRequest->save();
            $reqId = $appAdRequest->id;

        } else {
            
            $reqId = $appReqData->id;
            $secondsFromRequest = strtotime($currentTime) - strtotime($appReqData->request_time);

            $appAdRequest = app_ad_requests::find($appReqData->id);
            $appAdRequest->ad_current_status = $request->adCurrentStatus;
            $appAdRequest->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $appAdRequest->save();

            $adLastStatusData = ad_request_status::where('app_request_id', $appReqData->id)->where('ad_status', $request->adCurrentStatus - 1)->first();
            if($adLastStatusData){
                $lastStatusDateTime = strtotime($adLastStatusData->request_time);
                $secondsFromLastStatus = strtotime($currentTime) - $lastStatusDateTime;
            }
        }

        $adStatus = new ad_request_status();
        $adStatus->app_request_id = $reqId;
        $adStatus->ad_status = $request->adCurrentStatus;
        $adStatus->duration_last_status = $secondsFromLastStatus;
        $adStatus->duration_with_request = $secondsFromRequest;
        $adStatus->request_time = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $adStatus->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $adStatus->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        
        if($adStatus->save()){
            $TypeStatus = adTypeStatus($request->adType);
            $Status = adStatus($request->adCurrentStatus);
            $message = $TypeStatus . " " .$Status['type'] . " Successfully.";
            return $this->sendResponseSuccess($message);
        }

        return $this->sendResponseSuccess("Ad Request has been sent Failed.");
        
    }

    public function updateAdStatus(Request $request){

        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('Y-m-d H:i:s', time());
        
        $messages = [
            'userId.required' => 'User ID is Required',
            'adCurrentStatus.required' => 'Ad Current Status is Required',
            'adCurrentStatus.integer' => 'Ad Current Status should be Numeric',
            'adKey.required' => 'Ad Key is Required'
        ];

        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'adCurrentStatus' => 'required|integer',
            'adKey' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $appReqData = app_ad_requests::where('uniq_str_key',$request->adKey)->first();
        // dd($appReqData);
        $reqDateTime = strtotime($appReqData->request_time);

        $secondsFromRequest = strtotime($currentTime) - $reqDateTime;

        $secondsFromLastStatus = 0;
        $adLastStatusData = ad_request_status::where('app_request_id', $appReqData->id)->where('ad_status', $request->adCurrentStatus - 1)->first();
        if($adLastStatusData){
            $lastStatusDateTime = strtotime($adLastStatusData->request_time);
            $secondsFromLastStatus = strtotime($currentTime) - $lastStatusDateTime;
        }
        

        $isValidUser = $this->checkUserIsValidOrNot($request->userId);
        if ($isValidUser == false){
            return $this->sendError("This User doesn't available", "Invalid userId", []);
        }

        $adStatus = new ad_request_status();
        $adStatus->app_request_id = $appReqData->id;
        $adStatus->ad_status = $request->adCurrentStatus;
        $adStatus->duration_last_status = $secondsFromLastStatus;
        $adStatus->duration_with_request = $secondsFromRequest;
        $adStatus->request_time = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $adStatus->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $adStatus->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));

        $appAdRequest = app_ad_requests::find($appReqData->id);
        $appAdRequest->ad_current_status = $request->adCurrentStatus;
        $appAdRequest->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        
        if($adStatus->save() && $appAdRequest->save()){
            return $this->sendResponseSuccess("Ad Status has been Updated Successfully.");
        }

        return $this->sendResponseSuccess("Ad Status hasn't been Updated.");
        
    }


    public function packagePurchase(Request $request){

        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('Y-m-d H:i:s', time());

        $messages = [
            'userId.required' => 'User ID is Required',
            'appId.required' => 'App Id is Required',
            'packageId.required' => 'Package Id is Required'
        ];

        $validator = Validator::make($request->all(), [
            'userId' => 'required|integer',
            'appId' => 'required|integer',
            'packageId' => 'required|integer',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->userId)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $package = PriceRange::where('id',$request->packageId)->where('estatus',1)->first();
        if (!$package){
            return $this->sendError("Package Not Exist", "Not Found Error", []);
        }

        $application = Application::where('id',$request->appId)->where('estatus',1)->first();
        if (!$application){
            return $this->sendError("Application Not Exist", "Not Found Error", []);
        }

        $purchase = new Purchase();
        $purchase->user_id = $request->userId;
        $purchase->package_id = $request->packageId;
        $purchase->package_type = $package->package_type;
        $purchase->app_id = $request->appId;
        if($package->package_type == 2){
           $enddate = date("Y-m-d", strtotime("+ ".$package->value." day"));
           $purchase->end_date = $enddate;
        }
        $purchase->save();

        return $this->sendResponseSuccess("Package Purchase Successfully.");
        
    }

    public function getPackages(Request $request){
        $messages = [
            'appId.required' => 'App Id is Required',
        ];

        $validator = Validator::make($request->all(), [
            'appId' => 'required|integer',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }
        $application = Application::where('id',$request->appId)->where('estatus',1)->first();
        if (!$application){
            return $this->sendError("Application Not Exist", "Not Found Error", []);
        }
        $packages = PriceRange::where('application_id',$request->appId)->get();
        return $this->sendResponseWithData($packages,"Package Purchase Successfully.");
        
    }

    
}
