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

            $totalData = users_apps_visit::with('user');
            
            $totalData = $totalData->groupBy('user_id')->count();
            $totalFiltered = $totalData;
           // dd($totalFiltered);
            $limit = $request->input('length');
            $app_id = $request->input('app_id');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order == "created_at";
                $dir = 'desc';
            }
          
            if(empty($request->input('search.value')) && $request->start_date==null && $request->end_date==null)
            {
                $visitlogs = users_apps_visit::select(\DB::raw('*, max(created_at) as created_at'))->with('user')->WhereHas('user.application',function ($mainQuery) use($app_id) {
                    $mainQuery->where('app_id',$app_id);
                });
                $visitlogs = $visitlogs->offset($start)
                    ->limit($limit)
                    ->groupBy('user_id')
                    ->orderBy($order,$dir)
                    ->get();
            }else{
                $search = $request->input('search.value');
                $visitlogs =  users_apps_visit::select(\DB::raw('*, max(created_at) as created_at'))->with('user')->WhereHas('user.application',function ($mainQuery) use($app_id) {
                    $mainQuery->where('app_id',$app_id);
                });
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $visitlogs = $visitlogs->whereRaw("DATE(created_at) between '".$start_date."' and '".$end_date."'");
                }
                
                $visitlogs = $visitlogs->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                    ->orWhereHas('user',function ($mainQuery) use($search) {
                        $mainQuery->where('device_id', 'Like', '%' . $search . '%');
                    })->orWhereHas('user.application',function ($mainQuery) use($search) {
                        $mainQuery->where('app_name', 'Like', '%' . $search . '%');
                    });
                    })
                      ->offset($start)
                      ->limit($limit)
                      ->groupBy('user_id')
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = users_apps_visit::with('user')->WhereHas('user.application',function ($mainQuery) use($app_id) {
                    $mainQuery->where('app_id',$app_id);
                });
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $totalFiltered = $totalFiltered->whereRaw("DATE(created_at) between '".$start_date."' and '".$end_date."'");
                }
                
                $totalFiltered = $totalFiltered->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                    ->orWhereHas('user',function ($mainQuery) use($search) {
                        $mainQuery->where('device_id', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('user.application',function ($mainQuery) use($search) {
                        $mainQuery->where('app_name', 'Like', '%' . $search . '%');
                    });
                    })->groupBy('user_id')
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

                    $visitlogsss = users_apps_visit::with('user')->where('user_id',$visitlog->user->id)->WhereHas('user.application',function ($mainQuery) use($app_id) {
                        $mainQuery->where('app_id',$app_id);
                    })->orderBy($order,$dir)->get();

                    foreach ($visitlogsss as $key =>  $visitlogss){
                        //$item_details = json_decode($order_item->item_details,true);
                        //$ProductVariant = ProductVariant::where('id',$item_details['variantId'])->first();
                        $table .='<tr>';
                        $table .= '<td>  '.$item.'</td>';
                        $table .= '<td>  '.$visitlogss->user->id.'</td>';
                        $table .= '<td>  '.$visitlogss->user->device_os_version.'</td>';
                        $table .= '<td>  '.$visitlogss->user->device_company.'</td>';
                        $table .= '<td>  '.$visitlogss->user->device_model.'</td>';
                        $table .= '<td>  '.$visitlogss->user->device_id.'</td>';
                        $table .= '<td>  '.date('d-m-Y h:i A', strtotime($visitlogss->user->last_open_time)).'</td>';
                        $table .= '<td>  '.date('d-m-Y h:i A', strtotime($visitlogss->created_at)).'</td>';
                   
                        $table .= '</tr>';
                        $item++;
                    }
                    $table .='</table>';
                    
                    $nestedData['user_id'] = $visitlog->user->id;
                    $nestedData['device_os_version'] = $visitlog->user->device_os_version;
                    $nestedData['device_company'] = $visitlog->user->device_company;
                    $nestedData['device_model'] = $visitlog->user->device_model;
                    $nestedData['device_id'] = $visitlog->user->device_id;
                    // $nestedData['application'] = $visitlog->user->application->app_name;
                    $nestedData['first_open_time'] = date('d-m-Y h:i A', strtotime($visitlog->user->last_open_time));
                    $nestedData['open_time'] =  date('d-m-Y h:i A', strtotime($visitlog->created_at));
                    $nestedData['table1'] = $table;
                    $data[] = $nestedData;

                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval(count($visitlogs)),
                "recordsFiltered" => intval(count($visitlogs)),
                "data" => $data,
            );

            echo json_encode($json_data);
        }
    }
}
