<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Application;
use App\Models\Collection;
use App\Models\ContactMessage;
use App\Models\CustomerDeviceToken;
use App\Models\Notification;
use App\Models\PremiumUserTransaction;
use App\Models\ProductVariant;
use App\Models\Suggestion;
use App\Models\ {UserLevel, Level, User};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends BaseController
{
    public function verify_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required',
            'otp' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('mobile_no',$request->mobile_no)->where('otp',$request->otp)->where('estatus',1)->first();

        if ( $user && isset($user['otp_created_at']) ){
            $t1 = Carbon::parse(now());
            $t2 = Carbon::parse($user['otp_created_at']);
            $diff = $t1->diff($t2);
            // dd(Carbon::now()->toDateTimeString(),$user['otp_created_at'],$diff->i);
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save();

            if($diff->i > 30) {
                return $this->sendError('OTP verification Failed.', "verification Failed", []);
            }

            $data['token'] =  $user->createToken('MyApp')-> accessToken;
            $data['profile_data'] =  new UserResource($user);
            $final_data = array();
            array_push($final_data,$data);
            return $this->sendResponseWithData($final_data,'OTP verified successfully.');
        }
        else{
            return $this->sendError('OTP verification Failed.', "verification Failed", []);
        }
    }

    public function send_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('mobile_no',$request->mobile_no)->where('role',3)->where('estatus',1)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $data = array();
        $otp['otp'] =  mt_rand(1000,9999);
        send_sms($request->mobile_no, $otp['otp']);

        array_push($data,$otp);
        // $user->otp = $data['otp'];
        // $user->otp_created_at = Carbon::now();
        // $user->save();
        return $this->sendResponseWithData($data, "User OTP generated.");
    }

    public function edit_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'required',
            'email' => ['required', 'string', 'email', 'max:191',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->where('role', 3)->where('id','!=',$request->user_id)->where('estatus','!=',3);
            })],

        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $premiumuserid = User::whereNotNull('premiumuserid')->where('role',3)->orderBy('id', 'DESC')->first();
            //dd($premiumuserid);
        $preserid = 1;
        if($premiumuserid){
            $preserid  = $premiumuserid->premiumuserid + 1;
        }
       

        $user = User::find($request->user_id);
        if (!$user)
        {
            return $this->sendError('User Not Exist.', "Not Found Error", []);
        }
        $user->premiumuserid = $preserid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->full_name = $request->first_name." ".$request->last_name;
        $user->dob = $request->dob;
        if (isset($request->gender)) {
            $user->gender = $request->gender;
        }
        $user->email = isset($request->email) ? $request->email : null;

        if ($request->hasFile('profile_pic')) {
            if(isset($user->profile_pic)) {
                $old_image = public_path('images/profile_pic/' . $user->profile_pic);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }

            $image = $request->file('profile_pic');
            $ext = $image->getClientOriginalExtension();
            $ext = strtolower($ext);
            // $all_ext = array("png","jpg", "jpeg", "jpe", "jif", "jfif", "jfi","tiff","tif","raw","arw","svg","svgz","bmp", "dib","mpg","mp2","mpeg","mpe");
            $all_ext = array("png", "jpg", "jpeg");
            if (!in_array($ext, $all_ext)) {
                return $this->sendError('Invalid type of image.', "Extension error", []);
            }

            $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/profile_pic');
            $image->move($destinationPath, $image_name);
            $user->profile_pic = $image_name;
        }
        $user->save();

        return $this->sendResponseWithData(new UserResource($user),'User profile updated successfully.');
    }

    public function submit_refcode(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'referral_code' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->first();
        $user_referral_code = User::where('referral_id',$request->referral_code)->where('id','!=',$request->user_id)->where('estatus',1)->first();
        $total_users = User::where('parent_user_id', $user_referral_code->id)->count();

        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        if ($user->parent_user_id != 0){
            return $this->sendError("Referral Code Already Use", "Not Found Error", []);
        }

        if (!$user_referral_code){
            return $this->sendError("Invalid Referral Code", "Not Found Error", []);
        }

        if($total_users > 40){
            $user->upto_parentId = $user_referral_code->id;
            $user->save();
        }else{
            $user->parent_user_id = $user_referral_code->id;
            $user->save();
        }

        return $this->sendResponseSuccess("Referral Code Submitted Successfully");
    }

    public function check_refcode(Request $request){
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user_referral_code = User::where('referral_id',$request->referral_code)->where('estatus',1)->first();
       
        if (!$user_referral_code){
            return $this->sendError("Invalid Referral Code", "Not Found Error", []);
        }

        return $this->sendResponseSuccess("Valid Referral Code");
    }

    public function view_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('role',3)->where('estatus',1)->first();

        if (!$user){
            return $this->sendError("You can not view this profile", "Invalid user", []);
        }

        $user->use_referral = 0;
        if($user->is_premium == 1){
            $user->use_referral = 1; 
        }else if($user->parent_user_id != 0){
            $user->use_referral = 1;
        }
        $user->is_paytm_enable = false;
        $user->gpay_key = env('gpay_key');
        
        $data = array();
        $isPayTm = array("is_paytm_enable" => false);
        array_push($data,new UserResource($user));
        // array_push($data, new UserResource($isPayTm));
        return $this->sendResponseWithData($data, 'User profile Retrieved successfully.');
    }

    public function collections(){
        $collections = Collection::with('applicationdropdown')->where('estatus',1)->orderBy('sr_no','ASC')->get();
        $collections_arr = array();
        foreach ($collections as $collection){
            $temp = array();
            $temp['id'] = $collection->id;
            $temp['title'] = $collection->title;
            $temp['image'] = 'public/'.$collection->image;
            $temp['application_dropdown_id'] = $collection->application_dropdown_id;
            $temp['application_dropdown'] = $collection->applicationdropdown->title;

            if($collection->application_dropdown_id == 5){
                $category = Category::where('id',$collection->value)->first();
                $product = ProductVariant::where('id',$collection->product_variant_id)->pluck('product_title')->first();
                $temp['value_id'] = $collection->product_variant_id;
                $temp['value_title'] = $product;
            }
            elseif($collection->application_dropdown_id == 7){
                $category = Category::where('id',$collection->value)->first();
				if($category){
                   $temp['value_id'] = $category->id;
                   $temp['value_title'] = $category->category_name;
				}	
            }
            else{
                $temp['value_id'] = null;
                $temp['value_title'] = $collection->value;
            }

            array_push($collections_arr,$temp);
        }

        return $this->sendResponseWithData($collections_arr,"Collections Retrieved Successfully.");
    }

    public function registerUser(Request $request){
        $messages = [
            'deviceId.required' => 'Device ID is Required',
            'appBundle.required' => 'App Bundle is Required',
            'deviceType.required' => 'Device Type is Required'
        ];

        $validator = Validator::make($request->all(), [
            'deviceId' => 'required',
            'appBundle' => 'required',
            'deviceType' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }
        
        $appData = Application::where('app_bundle', $request->appBundle);
        if (!$appData)
        {
            return $this->sendError('Invalid App Bundle', "Not Found Error", []);
        }
        
        $user = User::where('app_id', $appData->id)->where('device_id', $request->deviceId);
        if (!$user)
        {
            
            $user = new User();
            $user->app_id = $appData->id;
            $user->role = 3;
            $user->device_id = $request->deviceId;
            $user->device_type = $request->deviceType;
            $user->device_company = $request->deviceCompany;
            $user->device_model = $request->deviceModel;
            $user->device_os_version = $request->deviceOsVersion;
            $user->estatus = 1;
            $user->last_open_time = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));

            $user->save();
            $newUserId = $user->id;
            // return $this->sendError('User Not Exist.', "Not Found Error", []);
            $msg = "User Registered Successfully";
        } 
        else {
            $newUserId = $user->id;
            $msg = "User Id Retrieved Successfully";
        }

        $data = array();
        $data['userId'] = $newUserId;

        return $this->sendResponseWithData($data, $msg);
    }

    public function update_membership(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'amount' => 'required',
            'transaction_id' => 'required',
            'payment_mode' => 'required',
            'transaction_date' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $premiumuserid = User::where('role',3)->orderBy('id', 'DESC')->first();
        $preserid = 1;
        if($premiumuserid){
            $preserid  = $premiumuserid->premiumuserid;
        }

        
        $user = User::where('id',$request->user_id)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $user->is_premium = 1;
        //$user->premiumuserid = $preserid + 1;
        $password_string = '!@#$%*&abcdefghijklmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ23456789';
        $password = substr(str_shuffle($password_string), 0, 12);
        $user->decrypted_password = $password;
        $user->password = Hash::make($password);
        $user->save();

        if ($user->is_premium == 1){
                $levels = Level::get();
                foreach ($levels as $level){
                    if($user){
                        $userlevel = new UserLevel();
                        $userlevel->user_id = $user->id;
                        $userlevel->level_id = $level->id;
                        $userlevel->commission_percentage = $level->commission_percentage;
                        $userlevel->no_child_users = $level->no_child_users;
                        $userlevel->save();
                    }
                }  
        }

        $premium_user_transaction = new PremiumUserTransaction();
        $premium_user_transaction->user_id = $request->user_id;
        $premium_user_transaction->amount = $request->amount;
        $premium_user_transaction->transaction_id = $request->transaction_id;
        $premium_user_transaction->payment_mode = $request->payment_mode;
        $premium_user_transaction->transaction_date = $request->transaction_date;
        $premium_user_transaction->save();

        $data = array();
        $temp['user_panel_url'] = "https://madnessuserpanel.matoresell.com";
        $temp['email'] = $user->email;
        $temp['password'] = $user->decrypted_password;
        array_push($data,$temp);
        return $this->sendResponseWithData($data,"User Membership updated.");
    }

    public function update_membershipnew(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'amount' => 'required',
            'transaction_id' => 'required',
            'payment_mode' => 'required',
            'transaction_date' => 'required',
            'adhar_front' => 'required|image|mimes:jpeg,png,jpg',
            'adhar_back' => 'required|image|mimes:jpeg,png,jpg',
            'adhar_card_no' => 'required|numeric|digits:12',
            'password' => 'required',
        ]);

        // dd($request->all());
        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        // $premiumuserid = User::where('is_premium',1)->orderBy('id', 'DESC')->first();
        // $preserid = 1;
        // if($premiumuserid){
        //     $preserid  = $premiumuserid->premiumuserid;
        // }
        $user_id = (int)$request->user_id;
        $user = User::where('id', $user_id)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error 1", []);
        }

        if ($user->parent_user_id != 0 && $request->referral_code != '' ){
            return $this->sendError("Referral Code Already Use", "Not Found Error 2", []);
        }

        if(isset($notification->application_dropdown_id)){

            $user_referral_code = User::where('referral_id',$request->referral_code)->where('estatus',1)->first();
            $total_users = User::where('parent_user_id', $user_referral_code->id)->count();

            if (!$user_referral_code){
                return $this->sendError("Invalid Referral Code", "Not Found Error 3", []);
            }

            if($total_users > 40){
                $user->upto_parentId = $user_referral_code->id;
            }else{
                $user->parent_user_id = $user_referral_code->id;
            }

       }

        

        $user->is_premium = 1;
        //$user->premiumuserid = $preserid + 1;
        // $password_string = '!@#$%*&abcdefghijklmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ23456789';
        // $password = substr(str_shuffle($password_string), 0, 12);
        // $user->decrypted_password = $password;
        // $user->password = Hash::make($password);
        //$user->password = $request->password;
        $user->password = Hash::make($request->password);
        $user->decrypted_password = $request->password;
        $user->adhar_card_no = $request->adhar_card_no;

        

        if ($request->hasFile('adhar_front')) {
            $image = $request->file('adhar_front');
            $image_name = 'adhar_front_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/adhar_front');
            $image->move($destinationPath, $image_name);
            $user->adhar_front = $image_name;
        }

        if ($request->hasFile('adhar_back')) {
            $image = $request->file('adhar_back');
            $image_name = 'adhar_back_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/adhar_back');
            $image->move($destinationPath, $image_name);
            $user->adhar_back = $image_name;
        }

        $user->save();

        if ($user->is_premium == 1){
            $levels = Level::get();
            foreach ($levels as $level){
                if($user){
                    $userlevel = new UserLevel();
                    $userlevel->user_id = $user->id;
                    $userlevel->level_id = $level->id;
                    $userlevel->commission_percentage = $level->commission_percentage;
                    $userlevel->no_child_users = $level->no_child_users;
                    $userlevel->save();
                }
            }
        }

        $premium_user_transaction = new PremiumUserTransaction();
        $premium_user_transaction->user_id = $request->user_id;
        $premium_user_transaction->amount = $request->amount;
        $premium_user_transaction->transaction_id = $request->transaction_id;
        $premium_user_transaction->payment_mode = $request->payment_mode;
        $premium_user_transaction->transaction_date = $request->transaction_date;
        $premium_user_transaction->save();

        $data = array();
        $temp['user_panel_url'] = "https://madnessuserpanel.matoresell.com";
        $temp['email'] = $user->email;
        $temp['password'] = $user->decrypted_password;
        array_push($data,$temp);
        return $this->sendResponseWithData($data,"User Membership updated.");
    }

    public function update_token(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'token' => 'required',
            'device_type' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $device = CustomerDeviceToken::where('user_id',$request->user_id)->first();
        if ($device){
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        else{
            $device = new CustomerDeviceToken();
            $device->user_id = $request->user_id;
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        $device->save();

        return $this->sendResponseSuccess("Device Token updated.");
    }

    public function notifications(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $notifications = Notification::with('applicationdropdown')->whereIn('user_id',[0,$request->user_id])->orderBy('created_at','DESC')->get();
        $notifications_arr = array();
        foreach ($notifications as $notification){
            $temp = array();
            $temp['id'] = $notification->id;
            $temp['title'] = $notification->notify_title;
            $temp['desc'] = $notification->notify_desc;
            $temp['image'] = isset($notification->notify_thumb)?'public/'.$notification->notify_thumb:null;
            $temp['application_dropdown_id'] = isset($notification->application_dropdown_id)?$notification->application_dropdown_id:0;
            $temp['application_dropdown'] = isset($notification->application_dropdown_id)?$notification->applicationdropdown->title:null;

            if($notification->application_dropdown_id == 5){
                $category = Category::where('id',$notification->parent_value)->first();
                $product = ProductVariant::where('id',$notification->value)->pluck('product_title')->first();
                $temp['value_id'] = $notification->value;
                $temp['value_title'] = $product;
            }
            elseif($notification->application_dropdown_id == 7){
                $category = Category::where('id',$notification->value)->first();
                $temp['value_id'] = $category->id;
                $temp['value_title'] = $category->category_name;
            }
            else{
                $temp['value_id'] = null;
                $temp['value_title'] = $notification->value;
            }

            $temp['type'] = $notification->type;
            array_push($notifications_arr,$temp);
        }

        return $this->sendResponseWithData($notifications_arr,"Notifications Retrieved Successfully.");
    }

    public function give_suggestion(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'message' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $suggestion = new Suggestion();
        $suggestion->user_id = $request->user_id;
        $suggestion->message = $request->message;
        $suggestion->save();

        return $this->sendResponseSuccess("Suggestion Submitted Successfully.");
    }

    public function contactMessage(Request $request){

        $messages = [
            'userId.required' => 'User ID is Required',
            'message.required' => 'Message is Required',
            'email.required' => 'Message is Required',
            
        ];

        $validator = Validator::make($request->all(), [
            'userId' => 'required|integer',
            'message' => 'required',
            'email' => 'required|email'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->userId)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

    
        $ContactMessage = new ContactMessage();
        $ContactMessage->user_id = $request->userId;
        $ContactMessage->message = $request->message;
        $ContactMessage->email = $request->email;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = 'contact_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/contactmessage');
            $image->move($destinationPath, $image_name);
            $ContactMessage->image = $image_name;
        }
        $ContactMessage->save();



        return $this->sendResponseSuccess("Message Send Successfully.");
        
    }
}
