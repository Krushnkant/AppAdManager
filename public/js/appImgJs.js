jQuery(document).ready(function(){
    var ImageUrl = $("#web_url").val() + "/admin/";
    //var FileforEditUrl = $("#web_url").val() + "/public/";
	var FileforEditUrl = $("#web_url").val();
	
    var appIconFile = $("#appIconFile").val();
    var FileforEdit = null;
	
    if (appIconFile != ''){
		var mimeType = null;
		var fileSize = null;
		$.ajax({
            type: 'GET',
            url: appIconFile,
            data: '',
            processData: false,
            contentType: false,
            success: function (res) {
				// var obj = jQuery.parseJSON(res);
				const obj = JSON.parse(res);
				mimeType = obj.mimeType;
				fileSize = obj.fileSize;
            },
            error: function (data) {
                
            }
        });

		FileforEdit = [{
			name: "aa", // file name
			size: fileSize, // file size in bytes
			type: mimeType, // file MIME type
			file: appIconFile, // file path
			local: '', // file path in listInput (optional)
			data: {
				thumbnail: FileforEditUrl+ '/' + appIconFile, // item custom thumbnail; if false will disable the thumbnail (optional)
				readerCrossOrigin: 'anonymous', // fix image cross-origin issue (optional)
				readerForce: true, // prevent the browser cache of the image (optional)
				readerSkip: true, // skip file from reading by rendering a thumbnail (optional)
				popup: false, // remove the popup for this file (optional)
				listProps: {}, // custom key: value attributes in the fileuploader's list (optional)
			}
		}]
		// console.log(FileforEditUrl+ '/' + appIconFile);
        // FileforEdit = [
            // {
            //     name: appIconFile,
            //     size: fileSize,
            //     type: mimeType,
            //     file: appIconFile,
            //     url: FileforEditUrl+ '/' + appIconFile
            // }
        // ];
		
		
    }

	jQuery("#appIconFiles").filer({
		limit: 1,
		maxSize: null,
		extensions: ["jpg", "jpeg", "png"],
		changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&Drop files here</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Files</a></div></div>',
		showThumbs: true,
		theme: "dragdropbox",
		templates: {
			box: '<ul class="jFiler-items-list jFiler-items-grid col-md-6 col-sm-12"></ul>',
			item: '<li class="jFiler-item">\
						<div class="jFiler-item-container">\
							<div class="jFiler-item-inner">\
								<div class="jFiler-item-thumb">\
									<div class="jFiler-item-status"></div>\
									<div class="jFiler-item-thumb-overlay">\
										<div class="jFiler-item-info">\
											<div style="display:table-cell;vertical-align: middle;">\
												<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
												<span class="jFiler-item-others">{{fi-size2}}</span>\
											</div>\
										</div>\
									</div>\
									{{fi-image}}\
								</div>\
								<div class="jFiler-item-assets jFiler-row">\
									<ul class="list-inline pull-left">\
										<li>{{fi-progressBar}}</li>\
									</ul>\
									<ul class="list-inline pull-right">\
										<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
									</ul>\
								</div>\
							</div>\
						</div>\
					</li>',
			itemAppend: '<li class="jFiler-item">\
							<div class="jFiler-item-container">\
								<div class="jFiler-item-inner">\
									<div class="jFiler-item-thumb">\
										<div class="jFiler-item-status"></div>\
										<div class="jFiler-item-thumb-overlay">\
											<div class="jFiler-item-info">\
												<div style="display:table-cell;vertical-align: middle;">\
													<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
													<span class="jFiler-item-others">{{fi-size2}}</span>\
												</div>\
											</div>\
										</div>\
										{{fi-image}}\
									</div>\
									<div class="jFiler-item-assets jFiler-row">\
										<ul class="list-inline pull-left">\
											<li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
										</ul>\
										<ul class="list-inline pull-right">\
											<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
										</ul>\
									</div>\
								</div>\
							</div>\
						</li>',
			progressBar: '<div class="bar"></div>',
			itemAppendToEnd: false,
			canvasImage: true,
			removeConfirmation: true,
			_selectors: {
				list: '.jFiler-items-list',
				item: '.jFiler-item',
				progressBar: '.bar',
				remove: '.jFiler-item-trash-action'
			}
		},
		dragDrop: {
			dragEnter: null,
			dragLeave: null,
			drop: null,
			dragContainer: null,
		},
		appendTo: "#uploadedImgBox",
		uploadFile: {
			url: ImageUrl+"appupdate/uploadfile?action=uploadAppIcon",
			data: {'_token': $('meta[name="csrf-token"]').attr('content')},
			type: 'POST',
			enctype: 'multipart/form-data',
			synchron: true,
			beforeSend: function(){},
			success: function(res, itemEl, listEl, boxEl, newInputEl, inputEl, id){
                // console.log(res, itemEl, listEl, boxEl, newInputEl, inputEl, id);
				var parent = itemEl.find(".jFiler-jProgressBar").parent(),
					new_file_name = res.data,
					filerKit = inputEl.prop("jFiler");
				jQuery("#appIconFile").val(new_file_name);
        		filerKit.files_list[id].name = new_file_name;

				itemEl.find(".jFiler-jProgressBar").fadeOut("slow", function(){
					jQuery("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
				});
			},
			error: function(el){
				var parent = el.find(".jFiler-jProgressBar").parent();
				el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
					jQuery("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
				});
			},
			statusCode: null,
			onProgress: null,
			onComplete: null
		},
		files: FileforEdit,
		addMore: false,
		allowDuplicates: true,
		clipBoardPaste: true,
		excludeName: null,
		beforeRender: null,
		afterRender: null,
		beforeShow: null,
		beforeSelect: null,
		onSelect: null,
		afterShow: null,
		onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
			var filerKit = inputEl.prop("jFiler"),
		        file_name = filerKit.files_list[id].name;
            var removableFile = jQuery("#appIconFile").val();
		    jQuery.post(ImageUrl+'appupdate/removefile?action=removeAppIcon', {'_token': $('meta[name="csrf-token"]').attr('content'), file: removableFile});
			jQuery("#appIconFile").removeAttr('value');
		},
		onEmpty: null,
		options: null,
		dialogs: {
			alert: function(text) {
				return alert(text);
			},
			confirm: function (text, callback) {
				confirm(text) ? callback() : null;
			}
		},
		captions: {
			button: "Choose Files",
			feedback: "Choose files To Upload",
			feedback2: "files were chosen",
			drop: "Drop file here to Upload",
			removeConfirmation: "Are you sure you want to remove this file?",
			errors: {
				filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
				filesType: "Only Images are allowed to be uploaded.",
				filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
				filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
			}
		}
	});

	// jQuery("#bannerIconFiles").filer({
	// 	limit: 1,
	// 	maxSize: null,
	// 	extensions: ["jpg", "jpeg", "png"],
	// 	changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&Drop files here</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Files</a></div></div>',
	// 	showThumbs: true,
	// 	theme: "dragdropbox",
	// 	templates: {
	// 		box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
	// 		item: '<li class="jFiler-item">\
	// 					<div class="jFiler-item-container">\
	// 						<div class="jFiler-item-inner">\
	// 							<div class="jFiler-item-thumb">\
	// 								<div class="jFiler-item-status"></div>\
	// 								<div class="jFiler-item-thumb-overlay">\
	// 									<div class="jFiler-item-info">\
	// 										<div style="display:table-cell;vertical-align: middle;">\
	// 											<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
	// 											<span class="jFiler-item-others">{{fi-size2}}</span>\
	// 										</div>\
	// 									</div>\
	// 								</div>\
	// 								{{fi-image}}\
	// 							</div>\
	// 							<div class="jFiler-item-assets jFiler-row">\
	// 								<ul class="list-inline pull-left">\
	// 									<li>{{fi-progressBar}}</li>\
	// 								</ul>\
	// 								<ul class="list-inline pull-right">\
	// 									<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
	// 								</ul>\
	// 							</div>\
	// 						</div>\
	// 					</div>\
	// 				</li>',
	// 		itemAppend: '<li class="jFiler-item">\
	// 						<div class="jFiler-item-container">\
	// 							<div class="jFiler-item-inner">\
	// 								<div class="jFiler-item-thumb">\
	// 									<div class="jFiler-item-status"></div>\
	// 									<div class="jFiler-item-thumb-overlay">\
	// 										<div class="jFiler-item-info">\
	// 											<div style="display:table-cell;vertical-align: middle;">\
	// 												<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
	// 												<span class="jFiler-item-others">{{fi-size2}}</span>\
	// 											</div>\
	// 										</div>\
	// 									</div>\
	// 									{{fi-image}}\
	// 								</div>\
	// 								<div class="jFiler-item-assets jFiler-row">\
	// 									<ul class="list-inline pull-left">\
	// 										<li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
	// 									</ul>\
	// 									<ul class="list-inline pull-right">\
	// 										<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
	// 									</ul>\
	// 								</div>\
	// 							</div>\
	// 						</div>\
	// 					</li>',
	// 		progressBar: '<div class="bar"></div>',
	// 		itemAppendToEnd: false,
	// 		canvasImage: true,
	// 		removeConfirmation: true,
	// 		_selectors: {
	// 			list: '.jFiler-items-list',
	// 			item: '.jFiler-item',
	// 			progressBar: '.bar',
	// 			remove: '.jFiler-item-trash-action'
	// 		}
	// 	},
	// 	dragDrop: {
	// 		dragEnter: null,
	// 		dragLeave: null,
	// 		drop: null,
	// 		dragContainer: null,
	// 	},
	// 	uploadFile: {
	// 		url: ImageUrl+"categories/uploadfile?action=uploadCatIcon",
	// 		data: {'_token': $('meta[name="csrf-token"]').attr('content')},
	// 		type: 'POST',
	// 		enctype: 'multipart/form-data',
	// 		synchron: true,
	// 		beforeSend: function(){},
	// 		success: function(res, itemEl, listEl, boxEl, newInputEl, inputEl, id){
    //             // console.log(res, itemEl, listEl, boxEl, newInputEl, inputEl, id);
	// 			var parent = itemEl.find(".jFiler-jProgressBar").parent(),
	// 				new_file_name = res.data,
	// 				filerKit = inputEl.prop("jFiler");
	// 			jQuery("#bannerImg").val(new_file_name);
    //     		filerKit.files_list[id].name = new_file_name;

	// 			itemEl.find(".jFiler-jProgressBar").fadeOut("slow", function(){
	// 				jQuery("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
	// 			});
	// 		},
	// 		error: function(el){
	// 			var parent = el.find(".jFiler-jProgressBar").parent();
	// 			el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
	// 				jQuery("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
	// 			});
	// 		},
	// 		statusCode: null,
	// 		onProgress: null,
	// 		onComplete: null
	// 	},
	// 	files: MobileFileforEdit,
	// 	addMore: false,
	// 	allowDuplicates: true,
	// 	clipBoardPaste: true,
	// 	excludeName: null,
	// 	beforeRender: null,
	// 	afterRender: null,
	// 	beforeShow: null,
	// 	beforeSelect: null,
	// 	onSelect: null,
	// 	afterShow: null,
	// 	onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
	// 		var filerKit = inputEl.prop("jFiler"),
	// 	        file_name = filerKit.files_list[id].name;
    //         var removableFile = jQuery("#bannerImg").val();
	// 	    jQuery.post(ImageUrl+'categories/removefile?action=removeCatIcon', {'_token': $('meta[name="csrf-token"]').attr('content'), file: removableFile});
	// 		jQuery("#bannerImg").removeAttr('value');
	// 	},
	// 	onEmpty: null,
	// 	options: null,
	// 	dialogs: {
	// 		alert: function(text) {
	// 			return alert(text);
	// 		},
	// 		confirm: function (text, callback) {
	// 			confirm(text) ? callback() : null;
	// 		}
	// 	},
	// 	captions: {
	// 		button: "Choose Files",
	// 		feedback: "Choose files To Upload",
	// 		feedback2: "files were chosen",
	// 		drop: "Drop file here to Upload",
	// 		removeConfirmation: "Are you sure you want to remove this file?",
	// 		errors: {
	// 			filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
	// 			filesType: "Only Images are allowed to be uploaded.",
	// 			filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
	// 			filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
	// 		}
	// 	}
	// });

	// var bannerImg = $("#bannerImg").val();
    // var MobileFileforEdit = null;
    // if (bannerImg!=''){
    //     MobileFileforEdit = [
    //         {
    //             name: bannerImg,
    //             size: 9453,
    //             type: "image/jpg",
    //             file: FileforEditUrl + bannerImg,
    //             url: FileforEditUrl + bannerImg
    //         }
    //     ];
    // }
	// jQuery("#reviewIconFiles").filer({
	// 	limit: 5,
	// 	maxSize: null,
	// 	extensions: ["jpg", "jpeg", "png"],
	// 	changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&Drop files here</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Files</a></div></div>',
	// 	showThumbs: true,
	// 	theme: "dragdropbox",
	// 	templates: {
	// 		box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
	// 		item: '<li class="jFiler-item">\
	// 					<div class="jFiler-item-container">\
	// 						<div class="jFiler-item-inner">\
	// 							<div class="jFiler-item-thumb">\
	// 								<div class="jFiler-item-status"></div>\
	// 								<div class="jFiler-item-thumb-overlay">\
	// 									<div class="jFiler-item-info">\
	// 										<div style="display:table-cell;vertical-align: middle;">\
	// 											<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
	// 											<span class="jFiler-item-others">{{fi-size2}}</span>\
	// 										</div>\
	// 									</div>\
	// 								</div>\
	// 								{{fi-image}}\
	// 							</div>\
	// 							<div class="jFiler-item-assets jFiler-row">\
	// 								<ul class="list-inline pull-left">\
	// 									<li>{{fi-progressBar}}</li>\
	// 								</ul>\
	// 								<ul class="list-inline pull-right">\
	// 									<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
	// 								</ul>\
	// 							</div>\
	// 						</div>\
	// 					</div>\
	// 				</li>',
	// 		itemAppend: '<li class="jFiler-item">\
	// 						<div class="jFiler-item-container">\
	// 							<div class="jFiler-item-inner">\
	// 								<div class="jFiler-item-thumb">\
	// 									<div class="jFiler-item-status"></div>\
	// 									<div class="jFiler-item-thumb-overlay">\
	// 										<div class="jFiler-item-info">\
	// 											<div style="display:table-cell;vertical-align: middle;">\
	// 												<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
	// 												<span class="jFiler-item-others">{{fi-size2}}</span>\
	// 											</div>\
	// 										</div>\
	// 									</div>\
	// 									{{fi-image}}\
	// 								</div>\
	// 								<div class="jFiler-item-assets jFiler-row">\
	// 									<ul class="list-inline pull-left">\
	// 										<li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
	// 									</ul>\
	// 									<ul class="list-inline pull-right">\
	// 										<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
	// 									</ul>\
	// 								</div>\
	// 							</div>\
	// 						</div>\
	// 					</li>',
	// 		progressBar: '<div class="bar"></div>',
	// 		itemAppendToEnd: false,
	// 		canvasImage: true,
	// 		removeConfirmation: true,
	// 		_selectors: {
	// 			list: '.jFiler-items-list',
	// 			item: '.jFiler-item',
	// 			progressBar: '.bar',
	// 			remove: '.jFiler-item-trash-action'
	// 		}
	// 	},
	// 	dragDrop: {
	// 		dragEnter: null,
	// 		dragLeave: null,
	// 		drop: null,
	// 		dragContainer: null,
	// 	},
	// 	uploadFile: {
	// 		url: ImageUrl+"categories/uploadfile?action=uploadCatIcon",
	// 		data: {'_token': $('meta[name="csrf-token"]').attr('content')},
	// 		type: 'POST',
	// 		enctype: 'multipart/form-data',
	// 		synchron: true,
	// 		beforeSend: function(){},
	// 		success: function(res, itemEl, listEl, boxEl, newInputEl, inputEl, id){
    //             // console.log(res, itemEl, listEl, boxEl, newInputEl, inputEl, id);
	// 			var parent = itemEl.find(".jFiler-jProgressBar").parent(),
	// 				new_file_name = res.data,
	// 				filerKit = inputEl.prop("jFiler");
	// 			jQuery("#appImage").val(new_file_name);
    //     		filerKit.files_list[id].name = new_file_name;

	// 			itemEl.find(".jFiler-jProgressBar").fadeOut("slow", function(){
	// 				jQuery("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
	// 			});
	// 		},
	// 		error: function(el){
	// 			var parent = el.find(".jFiler-jProgressBar").parent();
	// 			el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
	// 				jQuery("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
	// 			});
	// 		},
	// 		statusCode: null,
	// 		onProgress: null,
	// 		onComplete: null
	// 	},
	// 	files: FileforEdit,
	// 	addMore: false,
	// 	allowDuplicates: true,
	// 	clipBoardPaste: true,
	// 	excludeName: null,
	// 	beforeRender: null,
	// 	afterRender: null,
	// 	beforeShow: null,
	// 	beforeSelect: null,
	// 	onSelect: null,
	// 	afterShow: null,
	// 	onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
	// 		var filerKit = inputEl.prop("jFiler"),
	// 	        file_name = filerKit.files_list[id].name;
    //         var removableFile = jQuery("#appImage").val();
	// 	    jQuery.post(ImageUrl+'categories/removefile?action=removeCatIcon', {'_token': $('meta[name="csrf-token"]').attr('content'), file: removableFile});
	// 		jQuery("#appImage").removeAttr('value');
	// 	},
	// 	onEmpty: null,
	// 	options: null,
	// 	dialogs: {
	// 		alert: function(text) {
	// 			return alert(text);
	// 		},
	// 		confirm: function (text, callback) {
	// 			confirm(text) ? callback() : null;
	// 		}
	// 	},
	// 	captions: {
	// 		button: "Choose Files",
	// 		feedback: "Choose files To Upload",
	// 		feedback2: "files were chosen",
	// 		drop: "Drop file here to Upload",
	// 		removeConfirmation: "Are you sure you want to remove this file?",
	// 		errors: {
	// 			filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
	// 			filesType: "Only Images are allowed to be uploaded.",
	// 			filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
	// 			filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
	// 		}
	// 	}
	// });
});
