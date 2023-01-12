<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ isset($page) ? $page .' | App Ad Manager' : 'App Ad Manager' }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('photos/favicon.png') }}">
    <!-- Custom Stylesheet -->
    <link href="{{ asset('plugins/toastr/css/toastr.min.css')}}" rel="stylesheet">
    <link href="{{ asset('plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/jquery-asColorPicker-master/css/asColorPicker.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/summernote/dist/summernote.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

    <link href="{{ asset('css/jquery.filer.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery.filer-dragdropbox-theme.css') }}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link href="{{asset('css/custom-style.css')}}" rel="stylesheet">
{{--    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">--}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    {{--    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>--}}

</head>

<body>

<!--*******************
    Preloader start
********************-->
<div id="preloader">
    <div class="loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
        </svg>
    </div>
</div>
<!--*******************
    Preloader end
********************-->


<!--**********************************
    Main wrapper start
***********************************-->
<div id="main-wrapper">

    <!--**********************************
        Nav header start
    ***********************************-->
    <div class="nav-header">
        <div class="brand-logo">
            <a href="#">
                <b class="logo-abbr"><img src="{{ asset('photos/logo.png') }}" alt=""> </b>
                <span class="logo-compact"><img src="{{ asset('photos/logo-compact.png') }}" alt=""></span>
                <span class="brand-title text-white">
                 Application Ads 
                    <!-- <img src="{{ url('public/images/logo-text.png') }}" alt=""> -->
                </span>
            </a>
        </div>
    </div>
    <!--**********************************
        Nav header end
    ***********************************-->

    <!--**********************************
        Header start
    ***********************************-->
    <input type="hidden" name="web_url" value="{{ url("/") }}" id="web_url">
    <div class="header">
        <div class="header-content clearfix">

            <div class="nav-control">
                <div class="hamburger">
                    <span class="toggle-icon"><i class="icon-menu"></i></span>
                </div>
            </div>

            <div class="header-right">
                <ul class="clearfix">
                    <li class="icons dropdown">
                        <div class="user-img c-pointer position-relative"   data-toggle="dropdown">
                            <span class="activity active"></span>
                            <?php  
                               $user = \App\Models\User::where('id',Auth::user()->id)->where('estatus',1)->first();
                               if(isset($user->profile_pic) && $user->profile_pic != ""){
                            ?>
                               <img src="{{ $user->profile_pic }}" height="40" width="40" alt="">
                            <?php }else{ ?>
                                <img src="{{ asset('photos/avatar.jpg') }}" height="40" width="40" alt="">    
                            <?php } ?>    
                        </div>
                        <div class="drop-down dropdown-profile   dropdown-menu">
                            <div class="dropdown-content-body">
                                <ul>
                                     <li>
                                        <a href="{{ route('profile') }}"><i class="icon-lock"></i> <span>Profile</span></a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.logout') }}"><i class="icon-key"></i> <span>Logout</span></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--**********************************
        Header end ti-comment-alt
    ***********************************-->

    <!--**********************************
        Sidebar start
    ***********************************-->
    <div class="nk-sidebar">
        <div class="nk-nav-scroll">
            <ul class="metismenu" id="menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <i class="fa fa-dashboard"></i><span class="nav-text">Dashboard</span>
                    </a>
                </li>
                {{-- <li>
                    <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                        <i class="fa fa-list" aria-hidden="true"></i><span class="nav-text">Applications</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('admin.applications.create') }}">Add New Applications</a></li>
                        <li><a href="{{ route('admin.applications.list') }}">Applications List</a> </li>
                    </ul>
                </li> --}}
                <li>
                    <a href="{{ route('admin.applications.create') }}" aria-expanded="false">
                        <i class="fa fa-list" aria-hidden="true"></i><span class="nav-text"> Add New Applications
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.applications.list') }}" aria-expanded="false">
                        <i class="fa fa-list" aria-hidden="true"></i><span class="nav-text">Applications List
                    </a> 
                </li>
                {{-- <li>
                    <a href="{{ route('admin.users.list') }}" aria-expanded="false">
                        <i class="fa fa-users" aria-hidden="true"></i><span class="nav-text">Users
                    </span>
                    </a>
                </li> --}}
                <li>
                    <a href="{{ route('admin.purchase.list') }}" aria-expanded="false">
                        <i class="fa fa-users" aria-hidden="true"></i><span class="nav-text"> Purchases
                    </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.contactmessage.list') }}" aria-expanded="false">
                        <i class="fa fa-address-book" aria-hidden="true"></i><span class="nav-text"> Contact Message
                    </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.notifications.list') }}" aria-expanded="false">
                        <i class="fa fa-bell" aria-hidden="true"></i><span class="nav-text"> Notifications
                    </span>
                    </a>
                </li>
                
    

            </ul>
        </div>
    </div>
    <!--**********************************
        Sidebar end
    ***********************************-->

    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        @yield('content')
    </div>
    <!--**********************************
        Content body end
    ***********************************-->


    <!--**********************************
        Footer start
    ***********************************-->
    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; Designed & Developed by <a href="#">Web Vedant Technology</a> 2022</p>
        </div>
    </div>
    <!--**********************************
        Footer end
    ***********************************-->
</div>
<!--**********************************
    Main wrapper end
***********************************-->

<!--**********************************
    Scripts
***********************************-->

<script src="{{ asset('js/common.min.js') }}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/settings.js') }}"></script>
<script src="{{ asset('js/gleek.js') }}"></script>
<script src="{{ asset('js/styleSwitcher.js') }}"></script>
{{--<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>--}}
<!-- Toaster -->
<script src="{{ asset('plugins/toastr/js/toastr.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/js/toastr.init.js') }}"></script>
<!-- dataTable -->
<script src="{{ asset('plugins/tables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/tables/js/datatable-init/datatable-basic.min.js') }}"></script>

<script src="{{ asset('plugins/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>

<script src="{{ asset('plugins/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('plugins/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('plugins/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/plugins-init/form-pickers-init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

<script src="{{ asset('js/jquery.filer.min.js') }}" type="text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<script src="{{ asset('plugins/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/summernote/dist/summernote-init.js') }}"></script>

<script src="{{ url('plugins/chart.js/Chart.bundle.min.js') }}"></script>

<script>
 //$('.ckeditor').ckeditor();
   
//    CKEDITOR.replace('.ckeditor', {
//        filebrowserUploadUrl: "{{route('admin.categories.uploadfile', ['_token' => csrf_token() ])}}",
//        filebrowserUploadMethod: 'form'
//    });

</script>
@yield('js')

</body>
</html>

