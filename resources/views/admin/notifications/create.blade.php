<form class="form-valide" action="" id="NotificationForm" method="post" enctype="multipart/form-data">

    <div id="attr-cover-spin" class="cover-spin"></div>
    {{ csrf_field() }}
    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 container justify-content-center">


        <div class="form-group">
            <label class="col-form-label" for="Notification_Title">Applications <span class="text-danger">*</span>
            </label>
            <select name="app_id" id="app_id">
                <option></option>
                @foreach ($applications as $application)
                <option value="{{ $application->id }}">{{ $application->app_name }}</option>
                @endforeach
            </select>
            <div id="app_id-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="Notification_Title">Notification Title <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control input-flat" id="notify_title" name="notify_title" placeholder="">
            <div id="notify_title-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="Notification_Description">Notification Description <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control input-flat" id="notify_desc" name="notify_desc" placeholder="">
            <div id="notify_desc-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="Notification">Thumbnail  <span class="text-danger">*</span>
            </label>
            <input type="file" name="files[]" id="NotificationFiles" multiple="multiple">
            <input type="hidden" name="NotificationImg" id="NotificationImg" value="">
            <div id="NotificationImg-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="value">Click Show 
            </label>
            <input type="text" class="form-control input-flat" id="value" name="value" placeholder="">
            <div id="value-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        

        

      
        <button type="button" class="btn btn-outline-primary" id="save_newNotificationBtn" data-action="add">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>&nbsp;&nbsp;
        <button type="button" class="btn btn-primary" id="save_closeNotificationBtn" data-action="add">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>

    </div>
</form>

