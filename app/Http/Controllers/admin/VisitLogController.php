<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users_apps_visit;

class VisitLogController extends Controller
{
    public function index(){
        return view('admin.visit_log.list');
    }

    public function allvisitloglist(Request $request){
        if ($request->ajax()) {
       
            $columns = array(
                0 =>'id',
                1 =>'device_id',
                2=> 'application',
                3=> 'first_open_time',
                4=> 'open_time'
            );

            $totalData = users_apps_visit::with('user');
            
            $totalData = $totalData->count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order == "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')) && isset($request->start_date) && $request->start_date=="" && isset($request->end_date) && $request->end_date=="")
            {
                $visitlogs = users_apps_visit::with('user');
                $visitlogs = $visitlogs->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $visitlogs =  users_apps_visit::with('user');
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
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = users_apps_visit::with('user');
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
                    })
                    ->count();
            }

            $data = array();

            if(!empty($visitlogs))
            {
                foreach ($visitlogs as $visitlog)
                {
                    
                    $nestedData['device_id'] = $visitlog->user->device_id;
                    $nestedData['application'] = $visitlog->user->application->app_name;
                    $nestedData['first_open_time'] = date('d-m-Y h:i A', strtotime($visitlog->user->last_open_time));
                    $nestedData['open_time'] =  date('d-m-Y h:i A', strtotime($visitlog->created_at));
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
