@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Purchase Package </a></li>
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

                        {{-- <div class="action-section row">
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#PriceRangeModel" id="AddPriceRangeBtn"><i class="fa fa-plus" aria-hidden="true"></i></button> 
                            </div>
                            
                        </div> --}}

                        <div class="action-section mt-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control comman-filter" id="package_filter" name="package_filter">
                                        <option value=""></option>
                                        <option value="1">Product</option>
                                        <option value="2">Subscription</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_pricerange" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Package </th>
                                    <th>Package Type</th>
                                    <th>Application</th>
                                    <th>Device Id</th>
                                    <th>End Date</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Package </th>
                                    <th>Package Type</th>
                                    <th>Application</th>
                                    <th>Device Id</th>
                                    <th>End Date</th>
                                    <th>Date</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

 
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        pricerange_page_tabs('',true);
        $('#package_filter').select2({
            width: '100%',
            placeholder: "Select Package",
            allowClear: true
        });
    
    });

    function pricerange_page_tabs(tab_type='',is_clearState=false) {
        if(is_clearState){
            $('#all_pricerange').DataTable().state.clear();
        }
   
        var package_filter = $("#package_filter").val();
      
        $('#all_pricerange').DataTable({
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
                "url": "{{ url('admin/allpurchaseslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,package_filter:package_filter},
                // "dataSrc": ""
            },
            'columnDefs': [
                { "width": "50px", "targets": 0 },
                { "width": "145px", "targets": 1 },
                { "width": "165px", "targets": 2 },
                { "width": "75px", "targets": 3 },
                { "width": "120px", "targets": 4 },
                { "width": "115px", "targets": 5 },
                { "width": "115px", "targets": 6 },
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'package', name: 'package', class: "text-left multirow", orderable: false},
                {data: 'package_type', name: 'package_type', class: "text-left multirow", orderable: false},
                {data: 'application', name: 'application', class: "text-left multirow", orderable: false},
                {data: 'user', name: 'user', class: "text-center multirow", orderable: false},
                {data: 'end_date', name: 'end_date', class: "text-left multirow", orderable: false},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            ]
        });
    }
    

    $('body').on('change', '.comman-filter', function () {
        var tab_type = $(this).attr('data-tab');
        pricerange_page_tabs(tab_type,true);
    });

    function changePricerangeStatus(pricerange_id) {
        //var tab_type = get_users_page_tabType();
       
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changepricerangestatus') }}" +'/' + pricerange_id,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#PriceRangestatuscheck_"+pricerange_id).val(2);
                    $("#PriceRangestatuscheck_"+pricerange_id).prop('checked',false);
                    pricerange_page_tabs();
                    toastr.success("Package  Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#PriceRangestatuscheck_"+pricerange_id).val(1);
                    $("#PriceRangestatuscheck_"+pricerange_id).prop('checked',true);
                    pricerange_page_tabs();
                    toastr.success("Package  activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#AddPriceRangeBtn', function (e) {
        $("#PriceRangeModel").find('.modal-title').html("Add Package ");
    });

    $('body').on('click', '#editPriceRangeBtn', function () {
        var pricerange_id = $(this).attr('data-id');
        $.get("{{ url('admin/pricerange') }}" +'/' + pricerange_id +'/edit', function (data) {
            $('#PriceRangeModel').find('.modal-title').html("Edit Package ");
            $('#PriceRangeModel').find('#save_closePriceRangeBtn').attr("data-action","update");
            $('#PriceRangeModel').find('#save_newPriceRangeBtn').attr("data-action","update");
            $('#PriceRangeModel').find('#save_closePriceRangeBtn').attr("data-id",pricerange_id);
            $('#PriceRangeModel').find('#save_newPriceRangeBtn').attr("data-id",pricerange_id);
            $('#pricerange_id').val(data.id);
            
            $('#price').val(data.price);
            $('#title').val(data.title);
            $('#value').val(data.value);
           
            $("#package_type").val(data.package_type);
            
        })
    });

    $('body').on('click', '#deletePriceRangeBtn', function (e) {
        var delete_pricerange_id = $(this).attr('data-id');
        $("#DeletePriceRangeModel").find('#RemovePriceRangeSubmit').attr('data-id',delete_pricerange_id);
    });

    $('body').on('click', '#RemovePriceRangeSubmit', function (e) {
        $('#RemovePriceRangeSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_pricerange_id = $(this).attr('data-id');
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/pricerange') }}" +'/' + remove_pricerange_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeletePriceRangeModel").modal('hide');
                    $('#RemovePriceRangeSubmit').prop('disabled',false);
                    $("#RemovePriceRangeSubmit").find('.removeloadericonfa').hide();
                    pricerange_page_tabs();
                    toastr.success("Package  Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeletePriceRangeModel").modal('hide');
                    $('#RemovePriceRangeSubmit').prop('disabled',false);
                    $("#RemovePriceRangeSubmit").find('.removeloadericonfa').hide();
                    pricerange_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeletePriceRangeModel").modal('hide');
                $('#RemovePriceRangeSubmit').prop('disabled',false);
                $("#RemovePriceRangeSubmit").find('.removeloadericonfa').hide();
                pricerange_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    
    $('body').on('change', '#type', function () {
        if($(this).val() == 1){
            $("#Amount_label").html("Percentage (%) <span class='text-danger'>*</span>");
        }
        else if($(this).val() == 2){
            $("#Amount_label").html("Amount <span class='text-danger'>*</span>");
        }
    });

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.valueCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<!-- user list JS end -->
@endsection

