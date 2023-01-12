@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Application</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Notifications</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            @if(isset($action) && $action=='create')
                                Add Notification
                            @elseif(isset($action) && $action=='edit')
                                Edit Notification
                            @else
                                Notifications List
                            @endif
                        </h4>

                        <div class="action-section">
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary" id="AddNotificationBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        @if(isset($action) && $action=='list')
                            <div class="table-responsive">
                                <table id="Notification" class="table zero-configuration customNewtable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Image</th>
                                        <th>Notification Title</th>
                                        <th>Notification Desc</th>
                                        <th>Click Show</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>No.</th>
                                        <th>Image</th>
                                        <th>Notification Title</th>
                                        <th>Notification Desc</th>
                                        <th>Click Show</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif

                        @if(isset($action) && $action=='create')
                            @include('admin.notifications.create')
                        @endif

                        @if(isset($action) && $action=='edit')
                            @include('admin.notifications.edit')
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('js')
<script src="{{ url('public/js/NotificationImgJs.js') }}" type="text/javascript"></script>
<!-- Notification JS start -->
<script type="text/javascript">
$('body').on('click', '#AddNotificationBtn', function () {
    location.href = "{{ route('admin.notifications.add') }}";
});




$(document).ready(function() {
    notification_table(true);
    $('#app_id').select2({
        width: '100%',
        placeholder: "Select Application",
        allowClear: false
    });

});



$('body').on('click', '#save_closeNotificationBtn', function () {
    save_Notification($(this),'save_close');
});

$('body').on('click', '#save_newNotificationBtn', function () {
    save_Notification($(this),'save_new');
});

function save_Notification(btn,btn_type){
    $(btn).prop('disabled',true);
    $(btn).find('.loadericonfa').show();
    var action  = $(btn).attr('data-action');

    var formData = new FormData($("#NotificationForm")[0]);
    formData.append('action',action);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.notifications.save') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if(res.status == 'failed'){
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();

                if (res.errors.app_id) {
                    $('#app_id-error').show().text(res.errors.app_id);
                } else {
                    $('#app_id-error').hide();
                }

                if (res.errors.notify_title) {
                    $('#notify_title-error').show().text(res.errors.notify_title);
                } else {
                    $('#notify_title-error').hide();
                }

                if (res.errors.notify_desc) {
                    $('#notify_desc-error').show().text(res.errors.notify_desc);
                } else {
                    $('#notify_desc-error').hide();
                }

                if (res.errors.NotificationImg) {
                    $('#NotificationImg-error').show().text(res.errors.NotificationImg);
                } else {
                    $('#NotificationImg-error').hide();
                }

                if (res.errors.value) {
                    if($("#NotificationInfo").val() == 3) {
                        $('#value-error').show().text("Please provide a Price");
                    }
                    else if($("#NotificationInfo").val() == 5) {
                        $('#value-error').show().text("Please provide a Category");
                    }
                    else if($("#NotificationInfo").val() == 7) {
                        $('#value-error').show().text("Please provide a Category");
                    }
                    else if($("#NotificationInfo").val() == 10) {
                        $('#value-error').show().text("Please provide a Arrival Days");
                    }
                    else if($("#NotificationInfo").val() == 14) {
                        $('#value-error').show().text("Please provide a Banner URL");
                    }
                } else {
                    $('#value-error').hide();
                }
            }

            if(res.status == 200){
                if(btn_type == 'save_close'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.notifications.list')}}";
                    if(res.action == 'add'){
                        toastr.success("Notification Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Notification Updated",'Success',{timeOut: 5000});
                    }
                }
                if(btn_type == 'save_new'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.notifications.add')}}";
                    if(res.action == 'add'){
                        toastr.success("Notification Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Notification Updated",'Success',{timeOut: 5000});
                    }
                }
            }

        },
        error: function (data) {
            $(btn).prop('disabled',false);
            $(btn).find('.loadericonfa').hide();
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
}

function notification_table(is_clearState=false){
    if(is_clearState){
        $('#Notification').DataTable().state.clear();
    }

    $('#Notification').DataTable({
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
            "url": "{{ url('admin/allnotificationlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}'},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "50px", "targets": 0 },
            { "width": "120px", "targets": 1 },
            { "width": "170px", "targets": 2 },
            { "width": "240px", "targets": 3 },
            { "width": "150px", "targets": 4 },
            { "width": "100px", "targets": 5 },
            { "width": "100px", "targets": 6 },
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'notify_thumb', name: 'notify_thumb', orderable: false, searchable: false, class: "text-center"},
            {data: 'notify_title', name: 'notify_title', class: "text-left"},
            {data: 'notify_desc', name: 'notify_desc', class: "text-left"},
            {data: 'value', name: 'value', class: "text-left"},
            {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}



function removeuploadedimg(divId ,inputId, imgName){
    if(confirm("Are you sure you want to remove this file?")){
        $("#"+divId).remove();
        $("#"+inputId).removeAttr('value');
        var filerKit = $("#NotificationFiles").prop("jFiler");
        filerKit.reset();
    }
}

$('body').on('click', '#sendNotificationBtn', function (e) {
    $('#sendNotificationBtn').prop('disabled',true);
    e.preventDefault();
    var Notification_id = $(this).attr('data-id');
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/notifications') }}" +'/' + Notification_id +'/send',
        success: function (res) {
            if(res.status == 200){
                $('#sendNotificationBtn').prop('disabled',false);
                toastr.success("Notification Sent",'Success',{timeOut: 5000});
            }

            if(res.status == 400){
                $('#sendNotificationBtn').prop('disabled',false);
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $('#sendNotificationBtn').prop('disabled',false);
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

</script>
<!-- Notification JS end -->
@endsection
