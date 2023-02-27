@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Visit Log</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <div class="action-section">
                            <div class="row">
                                
                                <div class="col-md-3 input-group">
                                    <input type="text" autocomplete="off" class="form-control custom_date_picker comman-filter" id="start_date" name="start_date" placeholder="Start Date" data-date-format="yyyy-mm-dd" data-date-end-date="0d"> <span class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar-check"></i></span></span>
                                </div>
                                <div class="col-md-3 input-group">
                                    <input type="text" autocomplete="off" class="form-control custom_date_picker comman-filter" id="end_date" name="end_date" placeholder="End Date" data-date-format="yyyy-mm-dd" data-date-end-date="0d"> <span class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar-check"></i></span></span>
                                </div>
                                
                            </div>
                        </div>
                   

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_visitlog" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>User Id</th>
                                    <th>Device Company</th>
                                    <th>Device Model</th>
                                    <th>device OS Version</th>
                                    <th>Device Id</th>
                                    <th>First Open Time</th>
                                    <th>Open Time</th>
                                    
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>User Id</th>
                                    <th>Device Company</th>
                                    <th>Device Model</th>
                                    <th>device OS Version</th>
                                    <th>Device Id</th>
                                    <th>First Open Time</th>
                                    <th>Open Time</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img class="" src="#" width="400px" height="400px"/>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </div>
        </div>
    </div>

 
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        visitlog_page_tabs('',true);
        $('#package_filter').select2({
            width: '100%',
            placeholder: "Select Package",
            allowClear: true
        });

        $('#all_visitlog tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });
    
    });

    //$('.openimage').click(function(e) {
    //$('.openimage').click('.openimage', function () {    
    $('body').on('click', '.openimage', function () {
        console.log($(this));
        $('#myModal img').attr('src', $(this).attr('data-img-url')); 
    });

    $('body').on('change', '.comman-filter', function () {
        visitlog_page_tabs();
        
    });

    function format ( d ) {
        // `d` is the original data object for the row
        return d.table1;
    }

    function visitlog_page_tabs(tab_type='',is_clearState=false) {
        if(is_clearState){
            $('#all_visitlog').DataTable().state.clear();
        }
   
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var app_id = "{{ $id }}";
      
        table = $('#all_visitlog').DataTable({
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
                "url": "{{ url('admin/allvisitloglist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,start_date:start_date,end_date:end_date,app_id:app_id},
                // "dataSrc": ""
            },
            'columnDefs': [
                { "width": "50px", "targets": 0 },
                { "width": "50px", "targets": 1 },
                { "width": "120px", "targets": 2 },
                { "width": "120px", "targets": 3 },
                { "width": "120px", "targets": 4 },
                { "width": "120px", "targets": 5 },
                { "width": "150px", "targets": 6 },
                { "width": "150px", "targets": 7 },
                { "width": "150px", "targets": 8 },
            ],
            "columns": [
                {"className": 'details-control', "orderable": false, "data": null, "defaultContent": ''},
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'user_id', name: 'user_id', class: "text-center", orderable: true},
                {data: 'device_company', name: 'device_company', class: "text-left multirow", orderable: false},
                {data: 'device_model', name: 'device_model', class: "text-left multirow", orderable: false},
                {data: 'device_os_version', name: 'device_os_version', class: "text-left multirow", orderable: false},
                {data: 'device_id', name: 'device_id', class: "text-left multirow", orderable: false},
                {data: 'first_open_time', name: 'first_open_time', class: "text-center multirow", orderable: true},
                {data: 'open_time', name: 'open_time', class: "text-center multirow", orderable: true},
            ]
        });
    }

</script>
<!-- user list JS end -->
@endsection

