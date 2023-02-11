@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Application List</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{--<h4 class="card-title">User List</h4>--}}

                        <div class="action-section row">
                            <!-- <div class="col-lg-8 col-md-8 col-sm-12">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AppModal" id="AddUserBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>-->
                            <div class="custom-tab-1 col-lg-4">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item app_page_tabs" data-tab="0"><a class="nav-link active show" data-toggle="tab" href="#all_app_tab">All</a>
                                    </li>
                                    <li class="nav-item app_page_tabs" data-tab="1"><a class="nav-link" data-toggle="tab" href="#all_app_tab">Active</a>
                                    </li>
                                    <li class="nav-item app_page_tabs" data-tab="2"><a class="nav-link" data-toggle="tab" href="#all_app_tab">Deactive</a>
                                    </li>
                                </ul>
                            </div>
                        </div> 

                        <div class="tab-pane fade show active table-responsive" id="all_app_tab">
                            <table id="all_apps" class="table zero-configuration customNewtable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Application</th>
                                    <th>Bundle</th>
                                    <th>Users</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Other</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteAppModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Application</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Application?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveAppSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        app_page_tabs('',true);
    });

    function get_apps_page_tabType(){
        var tab_type;
        $('.app_page_tabs').each(function() {
            var thi = $(this);
            if($(thi).find('a').hasClass('show')){
                tab_type = $(thi).attr('data-tab');
            }
        });
        return tab_type;
    }

    $('#DeleteAppModal').on('hidden.bs.modal', function () {
        $(this).find("#RemoveAppSubmit").removeAttr('data-id');
    });

    function app_page_tabs(tab_type='',is_clearState=false) {
        if(is_clearState){
            $('#all_apps').DataTable().state.clear();
        }

        $('#all_apps').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            'stateSave': function(){
                if(is_clearState){
                    return false;
                }
                else{
                    return true;
                }
            },
            "ajax":{
                "url": "{{ url('admin/allappslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type},
                // "dataSrc": ""
            },
            'order': [[ 5, "DESC" ]],
            'columnDefs': [
                { "width": "8%", "targets": 0 },
                { "width": "18%", "targets": 1 },
                { "width": "17%", "targets": 2 },
                { "width": "10%", "targets": 3 },
                { "width": "8%", "targets": 4 },
                { "width": "12%", "targets": 5 },
                { "width": "12%", "targets": 6 },
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'application_details', name: 'application_details', class: "text-left"},
                {data: 'app_bundle', name: 'app_bundle', class: "text-left multirow", orderable: false},
                {data: 'users', name: 'users', class: "text-left multirow", orderable: false},
                {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center multirow"},
            ]
        });
    }

    $(".app_page_tabs").click(function() {
        var tab_type = $(this).attr('data-tab');
        app_page_tabs(tab_type,true);
    });

    function changeAppStatus(appId) {
        var tab_type = get_apps_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changeAppStatus') }}" +'/' + appId,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#appStatusCheck_"+appId).val(2);
                    $("#appStatusCheck_"+appId).prop('checked',false);
                    app_page_tabs(tab_type);
                    toastr.success("Application has been Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#appStatusCheck_"+appId).val(1);
                    $("#appStatusCheck_"+appId).prop('checked',true);
                    app_page_tabs(tab_type);
                    toastr.success("Application has been activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#editUserBtn', function () {
        var user_id = $(this).attr('data-id');
        $.get("{{ url('admin/users') }}" +'/' + user_id +'/edit', function (data) {
            // console.log(data);
            $('#AppModal').find('.modal-title').html("Edit User");
            $('#AppModal').find('#save_closeAppBtn').attr("data-action","update");
            $('#AppModal').find('#save_newAppBtn').attr("data-action","update");
            $('#AppModal').find('#save_closeAppBtn').attr("data-id",user_id);
            $('#AppModal').find('#save_newAppBtn').attr("data-id",user_id);
            $('#user_id').val(data.id);
            if(data.profile_pic==null){
                var default_image = "{{ asset('images/form-user.png') }}";
                $('#profilepic_image_show').attr('src', default_image);
            }
            else{
                var profile_pic =  data.profile_pic;
                $('#profilepic_image_show').attr('src', profile_pic);
            }
            $('#first_name').val(data.first_name);
            $('#middle_name').val(data.middle_name);
            $('#last_name').val(data.last_name);
            $('#mobile_no').val(data.mobile_no);
            $('#email').val(data.email);
            $('#dob').val(data.birth_date);
            $("#parentUser option[value=" + data.parent_id + "]").prop('selected', true);
            $("#zoneDropdown option[value=" + data.zone_id + "]").prop('selected', true);
            $("input[name=gender][value=" + data.gender + "]").prop('checked', true);
            $('#address').val(data.address);
        })
    });

    $('body').on('click', '.deleteAppBtn', function (e) {
        // e.preventDefault();
        var deleteAppId = $(this).attr('data-id');
        $("#DeleteAppModal").find('#RemoveAppSubmit').attr('data-id', deleteAppId);
    });

    $('body').on('click', '#RemoveAppSubmit', function (e) {
        $('#RemoveAppSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var removeAppId = $(this).attr('data-id');

        var tab_type = get_apps_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/applications') }}" +'/' + removeAppId +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeleteAppModal").modal('hide');
                    $('#RemoveAppSubmit').prop('disabled',false);
                    $("#RemoveAppSubmit").find('.removeloadericonfa').hide();
                    app_page_tabs(tab_type);
                    toastr.success("Application has been Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeleteAppModal").modal('hide');
                    $('#RemoveAppSubmit').prop('disabled',false);
                    $("#RemoveAppSubmit").find('.removeloadericonfa').hide();
                    app_page_tabs(tab_type);
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeleteAppModal").modal('hide');
                $('#RemoveAppSubmit').prop('disabled',false);
                $("#RemoveAppSubmit").find('.removeloadericonfa').hide();
                app_page_tabs(tab_type);
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });
</script>
<!-- user list JS end -->
@endsection

