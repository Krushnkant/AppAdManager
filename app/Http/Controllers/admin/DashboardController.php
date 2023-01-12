<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Opinion;
use App\Models\Inquiry;
use App\Models\ProjectPage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(){
        
        $yesterday_date = date('Y-m-d',strtotime("-1 days"));

        $today_user =  User::where('role',3)->whereDate('created_at', '=', date('Y-m-d'))->get();
        $today_user_count = $today_user->count();
        
        $yesterday_user =  User::where('role',3)->whereDate('created_at', '=', $yesterday_date)->get();
        $yesterday_user_count = $yesterday_user->count();                                  
        
        return view('admin.dashboard');
    }



    
}
