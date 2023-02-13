<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\app_ad_requests;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{
    private $page = "Application";

    public function index(){

        return view('admin.applications.list');
    }

    public function createAppPageData($id = 0){
        
        $application = array();
        if($id != 0){
            $action = "edit";

            $application = Application::find($id);
        }
        $action = "create";
        return view('admin.applications.create',compact('action', 'application'))->with('page',$this->page);
    }

    public function updateAppData(Request $request){
        $messages = [
            'appName.required' => 'Please provide a App Name',
            'appBundle.required' => 'Please provide a App Bundle',
            'clickInterval.required' => 'Please provide only Numeric Values',
            'clickInterSpalceval.required' => 'Please provide only Numeric Values',
            'timeInterval.required' => 'Please provide only Numeric Values'
        ];

        $validator = Validator::make($request->all(), [
            'appName' => 'required',
            'appBundle' => 'required',
            'clickInterval' => 'nullable|integer',
            'clickInterSpalceval' => 'nullable|integer',
            'timeInterval' => 'nullable|integer'
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $image_name = "";
        if(isset($request->action) && $request->action == "update"){

            $action = "update";
            $application = Application::find($request->appId);

            if(!$application){
                return response()->json(['status' => '400']);
            }
        }
        else{
            $action = "add";
            $application = new Application();
            $application->estatus = 1;
            $application->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        }

        $application->app_name = $request->appName;
        $application->app_bundle = $request->appBundle;
        $application->app_icon = $request->appIconFile;
        $application->interstitial1 = $request->interstitial1;
        $application->interstitial2 = $request->interstitial2;
        $application->native1 = $request->nativeBanner1;
        $application->native2 = $request->nativeBanner2;
        $application->reward1 = $request->reward1;
        $application->reward2 = $request->reward2;
        $application->interstitialreward1 = $request->interstitialreward1;
        $application->interstitialreward2 = $request->interstitialreward2;
        $application->banner1 = $request->banner1;
        $application->banner2 = $request->banner2;
        $application->app_open1 = $request->appOpen1;
        $application->app_open2 = $request->appOpen2;
        $application->click_event = $request->clickInterval;
        $application->interval_time = $request->timeInterval;
        $application->click_event_splash = $request->clickInterSpalceval;
        $application->splash_type = $request->splash_type;
        $application->service_key = $request->service_key;
        $application->app_url = $request->app_url;
        $application->app_version = $request->app_version;
        $application->custom_value = $request->custom_value;
        $application->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));

        $application->save();

        return response()->json(['status' => '200', 'action' => $action]);
    }
    

    public function allappslist(Request $request){
       
        if ($request->ajax()) {

            $estatus = $request->tab_type;

            // if ($tab_type == "active_user_tab"){
            //     $estatus = 1;
            // }
            // elseif ($tab_type == "deactive_user_tab"){
            //     $estatus = 2;
            // }

            $columns = array(
                0 => 'id',
                1 => 'application_details',
                2 => 'app_bundle',
                3 => 'users',
                4 => 'estatus',
                5 => 'created_at',
                6 => 'action'
            );

            $totalData = Application::where('estatus', '!=', 3);
            if ($estatus != 0){
                $totalData = $totalData->where('estatus',$estatus);
            }
            $totalData = $totalData->count();

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            // dd($columns[$request->input('order.0.column')]);
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            
            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            if($order == "application_details"){
                $order = "app_name";
            }

            if(empty($request->input('search.value')))
            {
                $applicationsData = Application::where('estatus', '!=', 3);
                if ($estatus != 0){
                    $applicationsData = $applicationsData->where('estatus',$estatus);
                }
                $applicationsData = $applicationsData->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $applicationsData =  Application::where('estatus', '!=', 3);
                if ($estatus != 0){
                    $applicationsData = $applicationsData->where('estatus',$estatus);
                }
                $applicationsData = $applicationsData->where(function($query) use($search){
                      $query->where('app_name', 'LIKE',"%{$search}%")
                            ->orWhere('app_bundle', 'LIKE',"%{$search}%")
                            ->orWhere('splash_interstitial', 'LIKE',"%{$search}%")
                            ->orWhere('interstitial', 'LIKE',"%{$search}%")
                            ->orWhere('native', 'LIKE',"%{$search}%")
                            ->orWhere('banner', 'LIKE',"%{$search}%")
                            ->orWhere('app_open', 'LIKE',"%{$search}%")
                            ->orWhere('created_at', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Application::where('estatus', '!=', 3);
                if ($estatus != 0){
                    $totalFiltered = $totalFiltered->where('estatus',$estatus);
                }
                $totalFiltered = $totalFiltered->where(function($query) use($search){
                        $query->where('app_name', 'LIKE',"%{$search}%")
                            ->orWhere('app_bundle', 'LIKE',"%{$search}%")
                            ->orWhere('splash_interstitial', 'LIKE',"%{$search}%")
                            ->orWhere('interstitial', 'LIKE',"%{$search}%")
                            ->orWhere('native', 'LIKE',"%{$search}%")
                            ->orWhere('banner', 'LIKE',"%{$search}%")
                            ->orWhere('app_open', 'LIKE',"%{$search}%")
                            ->orWhere('created_at', 'LIKE',"%{$search}%");
                        })
                        ->count();
            }

            $data = array();

            if(!empty($applicationsData))
            {
                // $i=1;
                foreach ($applicationsData as $appData)
                {

                    if( $appData->estatus == 1 ){
                        $estatus = '<label class="switch"><input type="checkbox" id="appStatusCheck_'. $appData->id .'" onchange="changeAppStatus('. $appData->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $appData->estatus == 2 ){
                        $estatus = '<label class="switch"><input type="checkbox" id="appStatusCheck_'. $appData->id .'" onchange="changeAppStatus('. $appData->id .')" value="2"><span class="slider round"></span></label>';
                    }

                    if(isset($appData->app_icon) && $appData->app_icon != null){
                        $app_icon = url($appData->app_icon);
                    }
                    else{
                        $app_icon = url('images/avatar.jpg');
                    }

                    $ad_info = '';
                    // if(isset($appData->interstitial1)){
                    //     $ad_info .= '<span>Interstitial: '.$appData->interstitial1.'</span>';
                    // }
                    // if(isset($appData->interstitial2)){
                    //     $ad_info .= '<span>Interstitial: '.$appData->interstitial2.'</span>';
                    // }
                    // if(isset($appData->native1)){
                    //     $ad_info .= '<span>Native Banner: '.$appData->native1.'</span>';
                    // }
                    // if(isset($appData->native2)){
                    //     $ad_info .= '<span>Native Banner: '.$appData->native2.'</span>';
                    // }
                    // if(isset($appData->banner1)){
                    //     $ad_info .= '<span>Banner: '.$appData->banner1.'</span>';
                    // }
                    // if(isset($appData->banner2)){
                    //     $ad_info .= '<span>Banner: '.$appData->banner2.'</span>';
                    // }
                    // if(isset($appData->app_open1)){
                    //     $ad_info .= '<span>App Open: '.$appData->app_open1.'</span>';
                    // }
                    // if(isset($appData->app_open2)){
                    //     $ad_info .= '<span>App Open: '.$appData->app_open2.'</span>';
                    // }
                    
                    $app_bundle = '';
                    if(isset($appData->app_bundle)){
                        $app_bundle = $appData->app_bundle;
                    }

                    $app_name = "";
                    if(isset($appData->app_name)){
                        $app_name = $appData->app_name;
                    }

                    $editUrl = url('admin/appupdate/'.$appData->id);
                    $reportUrl = url('admin/adsReport/'.$appData->id);
                    $productUrl = url('admin/pricerange/'.$appData->id);
                    $visitlogUrl = url('admin/visitlog/'.$appData->id);
                    
                    $action = '<a class="btn btn-gray text-blue btn-sm editUserBtn" data-id="' .$appData->id. '" href="'.$editUrl.'" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    $action .= '<button class="btn btn-gray text-danger btn-sm deleteAppBtn" data-toggle="modal" data-target="#DeleteAppModal" onclick="" data-id="' .$appData->id. '" Title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    $action .= '<a class="btn btn-warning btn-sm" data-id="' .$appData->id. '" href="'.$reportUrl.'" title="Report"><i class="fa fa-file" aria-hidden="true"></i></a>';
                    $action .= '<a class="btn btn-info btn-sm" title="Product"  href="'.$productUrl.'" title="Package"><i class="fa fa-product-hunt" aria-hidden="true"></i></a>';
                    $action .= '<a class="btn btn-info btn-sm" title="Visit Log"  href="'.$visitlogUrl.'" title="Package"><i class="fa fa-history" aria-hidden="true"></i></a>';
                    
                    // $nestedData['id'] = $i;
                    $nestedData['application_details'] = '<img src="'. $app_icon .'" width="40px" height="40px" alt="App Icon"><span class="ml-2">'.$app_name.'</span>';
                    $nestedData['app_bundle'] = $app_bundle;
                    $nestedData['users'] = User::where('estatus',1)->where('app_id', $appData->id)->count();
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($appData->created_at));
                    $nestedData['action'] = $action;
                    $data[] = $nestedData;
                    // $i=$i+1;
                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            // return json_encode($json_data);
            echo json_encode($json_data);
        }
    }

    public function changeAppStatus($id){
        $application = Application::find($id);
        if ($application->estatus == 1){
            $application->estatus = 2;
            $application->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($application->estatus == 2){
            $application->estatus = 1;
            $application->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function adsReportData($id){
        $page = 'Reports';
        return view('admin.report.list',compact('page','id'));
    }

    public function adsReportAllData(Request $request){
        if ($request->ajax()) {
              // dd($request->all());
            $app_id = $request->id;

            // if ($tab_type == "active_user_tab"){
            //     $estatus = 1;
            // }
            // elseif ($tab_type == "deactive_user_tab"){
            //     $estatus = 2;
            // }

            $columns = array(
                0 => 'id',
                1 => 'device_id',
                2 => 'uniq_str_key',
                3 => 'device_type',
                4 => 'ad_type',
                5 => 'status',
                6 => 'created_at'
            );
            //$user = User::where('app_id',$app_id)->get()->pluck('id')->toArray();
            $totalData = app_ad_requests::where('app_id',$app_id);
            $totalData = $totalData->count();
            $totalFiltered = $totalData;
            
            $limit = $request->input('length');
            $start = $request->input('start');
            // dd($columns[$request->input('order.0.column')]);
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            
            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

         

            if(empty($request->input('search.value')) && isset($request->status_filter) && $request->status_filter=="" && isset($request->start_date) && $request->start_date=="" && isset($request->end_date) && $request->end_date=="")
            {
                $applicationsData = app_ad_requests::with('ad_request_status')->where('app_id',$app_id);
                if (isset($request->type_filter) && $request->type_filter!=""){
                    $ad_type = $request->type_filter;
                    $applicationsData = $applicationsData->where('ad_type', $ad_type);
                }
                if (isset($request->status_filter) && $request->status_filter!=""){
                    $ad_status = $request->status_filter;
                    $applicationsData = $applicationsData->where('ad_current_status', $ad_status);
                }
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $applicationsData = $applicationsData->whereRaw("DATE(request_time) between '".$start_date."' and '".$end_date."'");
                }
                $applicationsData = $applicationsData->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
         
                $applicationsData =  app_ad_requests::with('ad_request_status')->where('app_id',$app_id);
                if (isset($request->type_filter) && $request->type_filter!=""){
                    $ad_type = $request->type_filter;
                    $applicationsData = $applicationsData->where('ad_type', $ad_type);
                }
                if (isset($request->status_filter) && $request->status_filter!=""){
                    $ad_status = $request->status_filter;
                    $applicationsData = $applicationsData->where('ad_current_status', $ad_status);
                }
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $applicationsData = $applicationsData->whereRaw("DATE(request_time) between '".$start_date."' and '".$end_date."'");
                }
              
                $applicationsData = $applicationsData->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                            ->orWhereHas('user',function ($mainQuery) use($search) {
                                $mainQuery->where('device_id', 'Like', '%' . $search . '%');
                            });
                    })->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();


                
                $totalFiltered = app_ad_requests::with('ad_request_status')->where('app_id',$app_id);      
                if (isset($request->type_filter) && $request->type_filter!=""){
                    $ad_type = $request->type_filter;
                    $totalFiltered = $totalFiltered->where('ad_type', $ad_type);
                }
                if (isset($request->status_filter) && $request->status_filter!=""){
                    $ad_status = $request->status_filter;
                    $totalFiltered = $totalFiltered->where('ad_current_status', $ad_status);
                }
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $totalFiltered = $totalFiltered->whereRaw("DATE(request_time) between '".$start_date."' and '".$end_date."'");
                }
              
                $totalFiltered = $totalFiltered->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                            ->orWhereHas('user',function ($mainQuery) use($search) {
                                $mainQuery->where('device_id', 'Like', '%' . $search . '%');
                            });
                    })->count();
               
            }

            $data = array();
               
            if(!empty($applicationsData))
            {
                // $i=1;
                // foreach ($applicationsData as $appData)
                // {
                //     $app_ad_requests = app_ad_requests::with('ad_request_status')->where('user_id',$appData->id);
                //     if (isset($request->status_filter) && $request->status_filter!=""){
                //         $ad_type = $request->status_filter;
                //         $app_ad_requests = $app_ad_requests->where('ad_type', $ad_type);
                //     }
                //     if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                //         $start_date = $request->start_date;
                //         $end_date = $request->end_date;
                //         $app_ad_requests = $app_ad_requests->whereRaw("DATE(request_time) between '".$start_date."' and '".$end_date."'");
                //     }
                //     $app_ad_requests = $app_ad_requests->get();
                    foreach ($applicationsData as $ad_request)
                    {
                        // if( $appData->estatus == 1 ){
                        //     $estatus = '<label class="switch"><input type="checkbox" id="appStatusCheck_'. $appData->id .'" onchange="changeAppStatus('. $appData->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                        // }

                        // if( $appData->estatus == 2 ){
                        //     $estatus = '<label class="switch"><input type="checkbox" id="appStatusCheck_'. $appData->id .'" onchange="changeAppStatus('. $appData->id .')" value="2"><span class="slider round"></span></label>';
                        // }

                        $estatus = "";
                        $table = '<table class="subclass text-left" cellpadding="6" cellspacing="0" border="0" style="padding-left:50px; width: 50%">';
                    $item = 1;
                    $table .='<tr>';
                    $table .= '<th>Status </th>';
                    $table .= '<th>Duration Last Status </th>';
                    $table .= '<th>Duration With Request </th>';
                    $table .= '<th>Request Time </th>';
                    $table .= '</tr>';
                    foreach ($ad_request->ad_request_status as $status_item){
                        //$item_details = json_decode($order_item->item_details,true);
                        //$ProductVariant = ProductVariant::where('id',$item_details['variantId'])->first();

                        $adStatus = "";
                        if(isset($status_item->ad_status)) {
                            $adStatus = adStatus($status_item->ad_status);
                            $adStatus = '<span class="'.$adStatus['class'].'">'.$adStatus['type'].'</span>';
                        }
                       
                        $table .='<tr>';
                        $table .= '<td>  '.$adStatus.'</td>';
                        $table .= '<td> '.$status_item->duration_last_status.' sec</td>';
                        $table .= '<td> '.$status_item->duration_with_request.' sec</td>';
                        $table .= '<td> '.date('d-m-Y h:i A', strtotime($status_item->request_time)).' </td>';
                        $table .= '</tr>';
                        $item++;
                    }
                    $table .='</table>';

                    $current_adStatus = "";
                    if(isset($ad_request->ad_current_status)) {
                        $current_adStatus = adStatus($ad_request->ad_current_status);
                        $current_adStatus = '<span class="'.$current_adStatus['class'].'">'.$current_adStatus['type'].'</span>';
                    }

                        
                        // $nestedData['id'] = $i;
                        $nestedData['device_id'] = $ad_request->user->device_id;
                        $nestedData['uniq_str_key'] = $ad_request->uniq_str_key;
                        $nestedData['device_type'] = DeviceTypeStatus($ad_request->user->device_type);
                        $nestedData['ad_type'] =  adTypeStatus($ad_request->ad_type);
                        $nestedData['status'] = $current_adStatus;
                        $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($ad_request->request_time));
                        $nestedData['table1'] = $table;
                        $data[] = $nestedData;
                        // $i=$i+1;
                    }
                //}
            }
            //dd($totalData);
            //dd($totalFiltered);
            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );
           
            // return json_encode($json_data);
            echo json_encode($json_data);
        }else{
            $page = 'Reports';
            return view('admin.report.list',compact('page','id'));
        }
    }

    public function deleteApp($id){
        $application = Application::find($id);
        if ($application){
            $image = $application->app_icon;
            $application->estatus = 3;
            $application->save();
            $application->delete();

            $image = public_path($image);
            if (file_exists($image)) {
                unlink($image);
            }
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function uploadfile(Request $request){
        if(isset($request->action) && $request->action == 'uploadAppIcon'){
            if ($request->hasFile('files')) {
                $image = $request->file('files')[0];
                $image_name = 'appThumb_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/appThumb');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/appThumb/'.$image_name]);
            }
        }
    }

    public function removefile(Request $request){
        if(isset($request->action) && $request->action == 'removeAppIcon'){
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
}
