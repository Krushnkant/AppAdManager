@extends('admin.layout')

@section('content')
    <?php
        $action = 'add';
        if(isset($application->app_name) && $application->app_name != ''){
            $action = 'update'; 
        }
    ?>
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Application List</a></li>
            </ol>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form class="form-valide" action="" id="applicationForm" method="post" enctype="multipart/form-data">
                            <div id="attr-cover-spin" class="cover-spin"></div>
                            {{ csrf_field() }}
                            <div class="container justify-content-center">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="appName">App Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control input-flat" id="appName" name="appName" value="{{ isset($application->app_name) ? ($application->app_name) : '' }}">
                                            <div id="appName-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="appBundle">App Bundle <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control input-flat" id="appBundle" name="appBundle" value="{{ isset($application->app_bundle) ? ($application->app_bundle) : '' }}">
                                            <div id="appBundle-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="appIcon">App Icon
                                            </label>
                                            <input type="file" name="files[]" id="appIconFiles" multiple="multiple">
                                            <input type="hidden" name="appIconFile" id="appIconFile" value="{{ isset($application->app_icon) ? ($application->app_icon) : '' }}">
                                            <div id="appIconFile-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="appIcon">App Icon</label>
                                        </div>
                                        <div id="uploadedImgBox">
                                        <div class="jFiler-items jFiler-row oldImgDisplayBox">
                                            <?php /*if(isset($application->app_icon)){ ?>
                                                <ul class="jFiler-items-list jFiler-items-grid">
                                                    <li id="ImgBox" class="jFiler-item" data-jfiler-index="1" style="">
                                                        <div class="jFiler-item-container">
                                                            <div class="jFiler-item-inner">
                                                                <div class="jFiler-item-thumb">
                                                                    <div class="jFiler-item-status"></div>
                                                                    <div class="jFiler-item-thumb-overlay"></div>
                                                                    <div class="jFiler-item-thumb-image"><img src="{{ url($application->app_icon) }}" draggable="false"></div>
                                                                </div>
                                                                <div class="jFiler-item-assets jFiler-row">
                                                                    <ul class="list-inline pull-right">
                                                                        <li><a class="icon-jfi-trash jFiler-item-trash-action" onclick="removeuploadedimg('ImgBox', 'catImg','<?php echo $application->app_icon;?>');"></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <?php }*/ ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="interstitial1">Interstitial
                                            </label>
                                            <input type="text" class="form-control input-flat" id="interstitial1" name="interstitial1" value="{{ isset($application->interstitial1) ? ($application->interstitial1) : '' }}">
                                            <div id="interstitial1-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="interstitial2">Interstitial
                                            </label>
                                            <input type="text" class="form-control input-flat" id="interstitial2" name="interstitial2" value="{{ isset($application->interstitial2) ? ($application->interstitial2) : '' }}">
                                            <div id="interstitial2-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="nativeBanner1">Native Banner
                                            </label>
                                            <input type="text" class="form-control input-flat" id="nativeBanner1" name="nativeBanner1" value="{{ isset($application->native1) ? ($application->native1) : '' }}">
                                            <div id="nativeBanner1-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="nativeBanner2">Native Banner
                                            </label>
                                            <input type="text" class="form-control input-flat" id="nativeBanner2" name="nativeBanner2" value="{{ isset($application->native2) ? ($application->native2) : '' }}">
                                            <div id="nativeBanner2-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="reward1">Reward
                                            </label>
                                            <input type="text" class="form-control input-flat" id="reward1" name="reward1" value="{{ isset($application->reward1) ? ($application->reward1) : '' }}">
                                            <div id="reward1-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="reward2">Reward
                                            </label>
                                            <input type="text" class="form-control input-flat" id="reward2" name="reward2" value="{{ isset($application->reward2) ? ($application->reward2) : '' }}">
                                            <div id="reward2-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="banner1">Banner
                                            </label>
                                            <input type="text" class="form-control input-flat" id="banner1" name="banner1" value="{{ isset($application->banner1) ? ($application->banner1) : '' }}">
                                            <div id="banner1-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="banner2">Banner
                                            </label>
                                            <input type="text" class="form-control input-flat" id="banner2" name="banner2" value="{{ isset($application->banner2) ? ($application->banner2) : '' }}">
                                            <div id="banner2-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="appOpen1">App Open
                                            </label>
                                            <input type="text" class="form-control input-flat" id="appOpen1" name="appOpen1" value="{{ isset($application->app_open1) ? ($application->app_open1) : '' }}">
                                            <div id="appOpen1-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="appOpen2">App Open
                                            </label>
                                            <input type="text" class="form-control input-flat" id="appOpen2" name="appOpen2" value="{{ isset($application->app_open2) ? ($application->app_open2) : '' }}">
                                            <div id="appOpen2-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="clickInterval">Click Interval to Show Ad
                                            </label>
                                            <input type="text" class="form-control input-flat" id="clickInterval" name="clickInterval" value="{{ isset($application->click_event) ? ($application->click_event) : '' }}">
                                            <div id="clickInterval-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="timeInterval">Time Interval to Show Ad
                                            </label>
                                            <input type="text" class="form-control input-flat" id="timeInterval" name="timeInterval" value="{{ isset($application->interval_time) ? ($application->interval_time) : '' }}">
                                            <div id="timeInterval-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="clickInterSpalceval">Click Interval to Splash Ad
                                            </label>
                                            <input type="text" class="form-control input-flat" id="clickInterSpalceval" name="clickInterSpalceval" value="{{ isset($application->click_event_splash) ? ($application->click_event_splash) : '' }}">
                                            <div id="clickInterSpalceval-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="splash_type">Splash Ad Type
                                            </label>
                                            
                                            <select class="form-control input-flat" name="splash_type" id="splash_type">
                                                <option value="1" {{ (isset($application->splash_type) && $application->splash_type == 1) ? 'selected' : '' }}>Interstitial</option>
                                                <option value="2" {{ (isset($application->splash_type) && $application->splash_type == 2) ? 'selected' : '' }}>App Open</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="service_key">Service Key
                                            </label>
                                            <input type="text" class="form-control input-flat" id="service_key" name="service_key" value="{{ isset($application->service_key) ? ($application->service_key) : '' }}">
                                            <div id="service_key-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-4">
                                    <input type="hidden" id="appId" name="appId" value="{{ isset($application->id) ? ($application->id) : '' }}">
                                    <button type="button" class="btn btn-outline-primary" id="save_newAppBtn" data-action="{{ $action }}">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                                    <button type="button" class="btn btn-primary ml-2" id="save_closeAppBtn" data-action="{{ $action }}">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection

@section('js')
<script src="{{ asset('js/appImgJs.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    function save_app(btn,btn_type){
        
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();
        var action  = $(btn).attr('data-action');
        console.log(action);
        var formData = new FormData($("#applicationForm")[0]);
        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/appupdate/updateAppData') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    if (res.errors.appIconFile) {
                        $('#appIconFile-error').show().text(res.errors.appIconFile);
                    } else {
                        $('#appIconFile-error').hide();
                    }

                    if (res.errors.appName) {
                        $('#appName-error').show().text(res.errors.appName);
                    } else {
                        $('#appName-error').hide();
                    }

                    if (res.errors.appBundle) {
                        $('#appBundle-error').show().text(res.errors.appBundle);
                    } else {
                        $('#appBundle-error').hide();
                    }

                    if (res.errors.clickInterval) {
                        $('#clickInterval-error').show().text(res.errors.clickInterval);
                    } else {
                        $('#clickInterval-error').hide();
                    }

                    if (res.errors.timeInterval) {
                        $('#timeInterval-error').show().text(res.errors.timeInterval);
                    } else {
                        $('#timeInterval-error').hide();
                    }

                    if (res.errors.clickInterSpalceval) {
                        $('#clickInterSpalceval-error').show().text(res.errors.clickInterSpalceval);
                    } else {
                        $('#clickInterSpalceval-error').hide();
                    }
                }

                if(res.status == 200){

                    if(res.action == 'add'){
                        toastr.success("Application has been Saved Successfully",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Application has been Updated Successfully",'Success',{timeOut: 5000});
                    }
                    if(btn_type == 'save_close'){
                        $(btn).prop('disabled', false);
                        $(btn).find('.loadericonfa').hide();
                        window.location.href = "{{ url('admin/applications') }}";
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        window.location.href = "{{ url('admin/appupdate') }}";
                    }
                    $('.invalid-feedback').html("");
                }

                if(res.status == 400){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newAppBtn', function () {
        save_app($(this),'save_new');
    });

    $('body').on('click', '#save_closeAppBtn', function () {
        save_app($(this),'save_close');
    });
</script>
@endsection