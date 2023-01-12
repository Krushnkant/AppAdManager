<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index(){
        return view('admin.contactmessage.list');
    }

    public function allcontactmessageslist(Request $request){
        if ($request->ajax()) {
       
            $columns = array(
                0 =>'id',
                1 =>'user',
                2=> 'email',
                3=> 'image',
                4=> 'message',
                5=> 'created_at'
            );

            $totalData = ContactMessage::with('user');
            
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
                $priceranges = ContactMessage::with('user');
                $priceranges = $priceranges->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $priceranges =  ContactMessage::with('user');
                
                $priceranges = $priceranges->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                    ->where('email','LIKE',"%{$search}%")
                    ->where('message','LIKE',"%{$search}%");
                    })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = ContactMessage::with('user');
                
                $priceranges = $priceranges->where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                    ->where('email','LIKE',"%{$search}%")
                    ->where('message','LIKE',"%{$search}%");
                    })
                    ->count();
            }

            $data = array();

            if(!empty($priceranges))
            {
                foreach ($priceranges as $pricerange)
                {
                    if(isset($pricerange->image) && $pricerange->image != null){
                        $image = '  <a href="#myModal" data-toggle="modal" data-gallery="example-gallery" class="col-sm-3 openimage" data-img-url="'. url('images/contactmessage/'.$pricerange->image) .'"><img src="'. url('images/contactmessage/'.$pricerange->image) .'" width="40px" height="40px" alt="Image"></a>';
                    }else{
                        $image = "";
                    }
                    $nestedData['user'] = $pricerange->user->device_id;
                    $nestedData['email'] = $pricerange->email;
                    $nestedData['image'] =$image;
                    $nestedData['message'] = $pricerange->message;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($pricerange->created_at));
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
