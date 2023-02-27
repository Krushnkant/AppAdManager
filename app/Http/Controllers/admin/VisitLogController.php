<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users_apps_visit;

class VisitLogController extends Controller
{
    public function index($id){
        return view('admin.visit_log.list',compact('id'));
    }

    public function allvisitloglist(Request $request){
       
        if ($request->ajax()) {
          
            $columns = array(
                0 =>'id',
                1 =>'user_id',
                2 =>'device_company',
                3 =>'device_model',
                4 =>'device_os_version',
                5 =>'device_id',
                6=> 'first_open_time',
                7=> 'open_time'
            );
            
            $app_id = $request->input('app_id');

            $totalData = users_apps_visit::join('users', 'users_apps_visits.user_id', '=', 'users.id')->where('users.app_id',$app_id);
            $totalData = $totalData->groupBy('user_id')->toSql();
            $totalFiltered = $totalData;
             dd($totalFiltered);
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order == "created_at";
                $dir = 'desc';
            }
          
            if(empty($request->input('search.value')) && $request->start_date==null && $request->end_date==null)
            {
                // $visitlogs = users_apps_visit::select(\DB::raw('*, max(created_at) as created_at'))->with('user')->WhereHas('user.application',function ($mainQuery) use($app_id) {
                //     $mainQuery->where('app_id',$app_id);
                // });
                $visitlogs = users_apps_visit::join('users', 'users_apps_visits.user_id', '=', 'users.id')->where('users.app_id',$app_id);
                $visitlogs = $visitlogs->offset($start)
                    ->limit($limit)
                    ->groupBy('user_id')
                    ->orderBy('users_apps_visits.created_at',$dir)
                    ->toSql();
                    dd($visitlogs);
            }else{
                $search = $request->input('search.value');
                // $visitlogs =  users_apps_visit::select(\DB::raw('*, max(created_at) as created_at'))->with('user')->WhereHas('user.application',function ($mainQuery) use($app_id) {
                //     $mainQuery->where('app_id',$app_id);
                // });
                $visitlogs = users_apps_visit::select(\DB::raw('*, max(users_apps_visits.created_at) as created_at,users_apps_visits.created_at as vscreated_at'))->join('users', 'users_apps_visits.user_id', '=', 'users.id')->where('users.app_id',$app_id);
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $visitlogs = $visitlogs->whereRaw("DATE(users_apps_visits.created_at) between '".$start_date."' and '".$end_date."'");
                }
                if($search != ""){
                    
                $visitlogs = $visitlogs->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                    ->orWhereHas('user',function ($mainQuery) use($search) {
                        $mainQuery->where('device_id', 'Like', '%' . $search . '%');
                    })->orWhereHas('user.application',function ($mainQuery) use($search) {
                        $mainQuery->where('app_name', 'Like', '%' . $search . '%');
                    });
                    });
                    
                }    
                $visitlogs = $visitlogs->offset($start)
                      ->limit($limit)
                      ->groupBy('user_id')
                      ->orderBy('users_apps_visits.created_at',$dir)
                      ->get();
                      //dd($visitlogs);
                // $totalFiltered = users_apps_visit::with('user')->WhereHas('user.application',function ($mainQuery) use($app_id) {
                //     $mainQuery->where('app_id',$app_id);
                // });
                $totalFiltered = users_apps_visit::join('users', 'users_apps_visits.user_id', '=', 'users.id')->where('users.app_id',$app_id);
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $totalFiltered = $totalFiltered->whereRaw("DATE(users_apps_visits.created_at) between '".$start_date."' and '".$end_date."'");
                }
                
                if($search != ""){
                $totalFiltered = $totalFiltered->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                    ->orWhereHas('user',function ($mainQuery) use($search) {
                        $mainQuery->where('device_id', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('user.application',function ($mainQuery) use($search) {
                        $mainQuery->where('app_name', 'Like', '%' . $search . '%');
                    });
                    });
                }  
                $totalFiltered = $totalFiltered->groupBy('user_id')
                    ->count();  
            }

            $data = array();

            if(!empty($visitlogs))
            {
                foreach ($visitlogs as $visitlog)
                {

                    $table = '<table class="subclass text-left" cellpadding="6" cellspacing="0" border="0" style="padding-left:50px; width: 100%">';
                    $item = 1;
                    $table .='<tr>';
                    $table .= '<th>No </th>';
                    $table .= '<th>User Id </th>';
                    $table .= '<th>Device Company </th>';
                    $table .= '<th>Device Model </th>';
                    $table .= '<th>Device OS Version </th>';
                    $table .= '<th>Device Id</th>';
                    $table .= '<th>First Open Time</th>';
                    $table .= '<th>Open Time</th>';
                    $table .= '</tr>';

                    // $visitlogsss = users_apps_visit::with('user')->where('user_id',$visitlog->user->id)->WhereHas('user.application',function ($mainQuery) use($app_id) {
                    //     $mainQuery->where('app_id',$app_id);
                    // })->orderBy($order,$dir)->get();

                    $visitlogsss = users_apps_visit::select('*','users_apps_visits.created_at as vscreated_at')->leftJoin('users', 'users_apps_visits.user_id', '=', 'users.id')->where('user_id',$visitlog->user->id)->where('users.app_id',$app_id)->orderBy('users_apps_visits.'.$order,$dir)->get();

                    foreach ($visitlogsss as $key =>  $visitlogss){
                        //$item_details = json_decode($order_item->item_details,true);
                        //$ProductVariant = ProductVariant::where('id',$item_details['variantId'])->first();
                        $table .='<tr>';
                        $table .= '<td>  '.$item.'</td>';
                        $table .= '<td>  '.$visitlogss->user_id.'</td>';
                        $table .= '<td>  '.$visitlogss->device_os_version.'</td>';
                        $table .= '<td>  '.$visitlogss->device_company.'</td>';
                        $table .= '<td>  '.$visitlogss->device_model.'</td>';
                        $table .= '<td>  '.$visitlogss->device_id.'</td>';
                        $table .= '<td>  '.date('d-m-Y h:i A', strtotime($visitlogss->last_open_time)).'</td>';
                        $table .= '<td>  '.date('d-m-Y h:i A', strtotime($visitlogss->vscreated_at)).'</td>';
                   
                        $table .= '</tr>';
                        $item++;
                    }
                    $table .='</table>';
                    
                    $nestedData['user_id'] = $visitlog->user->id;
                    $nestedData['device_os_version'] = $visitlog->device_os_version;
                    $nestedData['device_company'] = $visitlog->device_company;
                    $nestedData['device_model'] = $visitlog->device_model;
                    $nestedData['device_id'] = $visitlog->device_id;
                    // $nestedData['application'] = $visitlog->user->application->app_name;
                    $nestedData['first_open_time'] = date('d-m-Y h:i A', strtotime($visitlog->last_open_time));
                    $nestedData['open_time'] =  date('d-m-Y h:i A', strtotime($visitlog->created_at));
                    $nestedData['table1'] = $table;
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
}
