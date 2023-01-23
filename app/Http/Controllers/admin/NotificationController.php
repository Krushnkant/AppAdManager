<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\Models\Application;

class NotificationController extends Controller
{
    private $page = "Notifications";

    public function index(){
        $action = "list";
        return view('admin.notifications.list',compact('action'))->with('page',$this->page);
    }

    public function create(){
        $action = "create";
        $applications = Application::where('estatus',1)->get();
        return view('admin.notifications.list',compact('action','applications'))->with('page',$this->page);
    }

   
    public function uploadfile(Request $request){
        if(isset($request->action) && $request->action == 'uploadNotificationImg'){
            if ($request->hasFile('files')) {
                $image = $request->file('files')[0];
                $image_name = 'Notification_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/Notification');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/Notification/'.$image_name]);
            }
        }
    }

    public function removefile(Request $request){
        if(isset($request->action) && $request->action == 'removeNotificationImg'){
            $image = $request->file;

            if(isset($image)) {
                $image = public_path($request->file);
                if (file_exists($image)) {
                    unlink($image);
                    return response()->json(['status' => '200']);
                }
            }
        }
    }

    public function save(Request $request){
        $messages = [
            'NotificationImg.required' =>'Please provide a Notification Image',
            'notify_title.required' =>'Please provide a Notification Title',
            'notify_desc.required' =>'Please provide a Notification Description',
            'app_id.required' =>'Please Selecte Application',
        ];

        $validator = Validator::make($request->all(), [
            'NotificationImg' => 'required',
            'notify_title' => 'required',
            'notify_desc' => 'required',
            'app_id' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $action = "add";
        $Notification = new Notification();
        $Notification->app_id = $request->app_id;
        $Notification->notify_title = $request->notify_title;
        $Notification->notify_desc = $request->notify_desc;
        $Notification->notify_thumb = $request->NotificationImg;
        $Notification->value = $request->value;
        $Notification->click_value = $request->click_value;
        $Notification->save();
        $lastinsertid = $Notification->id;

        $notificationarray = Notification::where('id',$lastinsertid)->first();

        $notification_array['app_id'] = $notificationarray->app_id;
        $notification_array['notification_id'] = $notificationarray->id;
        $notification_array['notification_title'] = $notificationarray->notify_title;
        $notification_array['image'] = $notificationarray->notify_thumb;
        $notification_array['value'] = $notificationarray->value;
        $notification_array['click_value'] = $notificationarray->click_value;
        //send notification to customers
        if ($action == "add"){
            $notification_array['title'] = $Notification->notify_title;
            $notification_array['message'] = $Notification->notify_desc;
            // $notification_array['notificationdata'] = $notification_arr;
            $notification_array['image'] = isset($Notification->notify_thumb)?$Notification->notify_thumb:"";
            //print_r($notification_array); die;
            sendPushNotification_customers($notification_array);
        }

        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allnotificationlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'notify_thumb',
                2 => 'notify_title',
                3 => 'notify_desc',
                4 => 'application_dropdown_id',
                5 => 'value',
                6 => 'created_at',
                7 => 'action',
            );
            $totalData = Notification::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $Notifications = Notification::with('application')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Notifications =  Notification::with('application')
                    
                    ->where(function($query) use($search){
                        $query->where('notify_title','LIKE',"%{$search}%")
                            ->orWhere('notify_desc','LIKE',"%{$search}%")
                            ->orWhere('value','LIKE',"%{$search}%");
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($Notifications->toArray());
            }

            $data = array();

            if(!empty($Notifications))
            {
                foreach ($Notifications as $Notification)
                {
                    $action='';
                    $action .= '<button id="sendNotificationBtn" class="btn btn-gray text-warning btn-sm" data-id="' .$Notification->id. '"><i class="fa fa-bell-o" aria-hidden="true"></i></button>';
                    $img_path = url('public/'.$Notification->notify_thumb);
                    $nestedData['notify_thumb'] = '<img src="'. $img_path .'" width="50px" height="50px" alt="Notification Image">';
                    $nestedData['notify_title'] = $Notification->notify_title;
                    $nestedData['notify_desc'] = $Notification->notify_desc;
                    $nestedData['value'] = $Notification->value;
                    $nestedData['click_value'] = $Notification->click_value;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($Notification->created_at));
                    $nestedData['action'] = $action;
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        }
    }

   

    public function sendnotification($id){
        $Notification = Notification::where('id',$id)->first();

        if (!$Notification){
            return ['status' => 400];
        }

        $notificationarray = Notification::with('application')->where('id',$id)->first();

        // $notification_arr = array();
        // $notification_arr['notification_id'] = $notificationarray->id;
        // $notification_arr['notification_title'] = $notificationarray->notify_title;
        // $notification_arr['image'] = "public/".$notificationarray->notify_thumb;
        // $notification_arr['application_dropdown_id'] = $notificationarray->applicationdropdown->id;
        // $notification_arr['application_dropdown'] = $notificationarray->applicationdropdown->title;


        $notification_array['app_id'] = $notificationarray->app_id;
        $notification_array['notification_id'] = $notificationarray->id;
        $notification_array['notification_title'] = $notificationarray->notify_title;
        $notification_array['image'] = $notificationarray->notify_thumb;
        $notification_array['value'] = $notificationarray->value;
        $notification_array['click_value'] = $notificationarray->click_value;
        

        $notification_array['title'] = $Notification->notify_title;
        $notification_array['message'] = $Notification->notify_desc;
        // $notification_array['notificationdata'] = $notification_arr;
        $notification_array['image'] = isset($Notification->notify_thumb)?$Notification->notify_thumb:"";
        // print_r($notification_array); //die();
        sendPushNotification_customers($notification_array);
        return ['status' => 200];
    }
}
