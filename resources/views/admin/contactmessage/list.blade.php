@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Contact Message </a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                   

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_pricerange" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User </th>
                                    <th>Email</th>
                                    <th>Image</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>User </th>
                                    <th>Email</th>
                                    <th>Image</th>
                                    <th>Message</th>
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
        pricerange_page_tabs('',true);
        $('#package_filter').select2({
            width: '100%',
            placeholder: "Select Package",
            allowClear: true
        });
    
    });

    //$('.openimage').click(function(e) {
    //$('.openimage').click('.openimage', function () {    
    $('body').on('click', '.openimage', function () {
        console.log($(this));
        $('#myModal img').attr('src', $(this).attr('data-img-url')); 
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
                "url": "{{ url('admin/allcontactmessageslist') }}",
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
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'user', name: 'user', class: "text-left multirow", orderable: false},
                {data: 'email', name: 'email', class: "text-left multirow", orderable: false},
                {data: 'image', name: 'image', class: "text-left multirow", orderable: false},
                {data: 'message', name: 'message', class: "text-center multirow", orderable: false},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            ]
        });
    }

</script>
<!-- user list JS end -->
@endsection

