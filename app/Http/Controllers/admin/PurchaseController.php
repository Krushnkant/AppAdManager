<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function index(){
        $applications = Application::where('estatus',1)->get();
        return view('admin.purchase.list',compact('applications'));
    }

    public function allpurchaseslist(Request $request){
        if ($request->ajax()) {
       
            $columns = array(
                0 =>'id',
                1 =>'package',
                2=> 'package_type',
                3=> 'application',
                4=> 'user',
                5=> 'end_date',
                6=> 'created_at'
            );

            $totalData = Purchase::with('application','user','package');
           
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

            if(empty($request->input('search.value')))
            {
                $priceranges = Purchase::with('application','user','package');
                if (isset($request->package_filter) && $request->package_filter !=""){
                    $priceranges = $priceranges->where('package_type',$request->package_filter);
                }
                if (isset($request->application_filter) && $request->application_filter !=""){
                    $priceranges = $priceranges->where('app_id',$request->application_filter);
                }
                $priceranges = $priceranges->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $priceranges =  Purchase::with('application','user','package');
                if (isset($request->package_filter) && $request->package_filter !=""){
                    $priceranges = $priceranges->where('package_type',$request->package_filter);
                }
                if (isset($request->application_filter) && $request->application_filter !=""){
                    $priceranges = $priceranges->where('app_id',$request->application_filter);
                }
                $priceranges = $priceranges->where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Purchase::with('application','user','package');
                if (isset($request->package_filter) && $request->package_filter !=""){
                    $priceranges = $priceranges->where('package_type',$request->package_filter);
                }
                if (isset($request->application_filter) && $request->application_filter !=""){
                    $priceranges = $priceranges->where('app_id',$request->application_filter);
                }
                $priceranges = $priceranges->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%");
                    })
                    ->count();
            }

            $data = array();

            if(!empty($priceranges))
            {
                foreach ($priceranges as $pricerange)
                {
                  
                    if( $pricerange->package_type==1){
                        $package_type = 'Product';
                    }
                    if( $pricerange->package_type==2){
                        $package_type = 'Subscription';
                    }
                    $nestedData['package'] = $pricerange->package->title;
                    $nestedData['package_type'] = $package_type;
                    $nestedData['application'] = $pricerange->application->app_name;
                    $nestedData['user'] = $pricerange->user->device_id;
                    $nestedData['end_date'] = date('Y-m-d', strtotime($pricerange->end_date));
                    $nestedData['created_at'] = date('Y-m-d H:i A', strtotime($pricerange->created_at));
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
