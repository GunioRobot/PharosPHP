<script type="text/javascript">

	fileSizeLimit = "<?=$data['filesize_limit']?>";
	uploadCompleteText = "<?=($data['uploadCompleteText']!=''?$data['uploadCompleteText']:'You can now save your text changes and continue.')?>";
	
	function fileDialogComplete(numFilesSelected, numFilesQueued) {
		try {
			if (numFilesQueued > 0) {
				
				this.startUpload();
				
			}
		} catch (ex) {
			this.debug(ex);
		}
	}
	

	function uploadStart(file) {
		
		uploadInProgress = true;
		uploadingFile = file;
		
		if ( $('#dialog').length ) {
			$('#dialog').remove();
		}
			
		var div = $('<div id="dialog"><div id="progressbar"</div></div>').appendTo($('body'));
		div.dialog({
			bgiframe: true,
			modal: true,
			resizable:false,
			title : 'Uploading File...',
			beforeclose: function(event, ui) {
				return !uploadInProgress;
			},
			buttons : {
				"Cancel" : function() {
					var progressDialog = $(this);
					jConfirm('Are you sure you want to cancel this upload?', 'Cancel Upload', function(r) {
						if ( r ) {
							swfu.cancelUpload(uploadingFile.id, false);
							progressDialog.dialog('close');
						}
					});
				}
			}
		});		
				
		$('#progressbar').progressbar({value : 1});
		
		return true;
	}
	
	function uploadProgress(file, bytesProcessed, totalBytes) {
		$('#progressbar').progressbar('option', 'value', (bytesProcessed/totalBytes)*100);
	}
	
	function uploadSuccess(file, server_data, receivedResponse) {
		savedFileName = server_data != 'false' ? server_data : '';
	}
	
	function fileQueueError(file, errorCode, message) {
		
		var dialogMessage = '';
		var dialogTitle = '';
		
		switch ( errorCode ) {
			
			case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED :
				dialogTitle = 'Max Uploads';
				dialogMessage = 'You have reached the maximum number of allowed uploads.';
				break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT :
				dialogTitle = 'File Too Large';
				dialogMessage = '&quot;'+file.name+'&quot; is too large and exceeds the maximum limit of '+fileSizeLimit+'.';
				break;
			
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE :
				dialogTitle = 'Empty File';
				dialogMessage = '&quot;'+file.name+'&quot; is empty.';
				break;
						
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE :
				dialogTitle = 'Invalid File Type';
				dialogMessage = '&quot;'+file.name+'&quot; is an unsupported file.';
				break;
		}
			
		var div = $('<p>'+dialogMessage+'</p>').appendTo($('body'));
		div.dialog({
			bgiframe: true,
			modal: true,
			resizable:false,
			title : dialogTitle,
			buttons : {
				"Ok" : function() { 
					$(this).dialog('close');
				}
			}
		});
		
	}
	
	function uploadComplete(file) {
		
		if ( file.filestatus == SWFUpload.FILE_STATUS.CANCELLED ) {
			
			uploadInProgress = false;
									
			$('#dialog').dialog('option', 'title', 'Upload Cancelled');
			$('#dialog').html('&quot;'+file.name+'&quot; has been cancelled.');
			$('#dialog').dialog('option', 'buttons', {
				"Ok" : function() { 
					$('#dialog').dialog('close'); 
				} 
			});		

				
		} else {
			
			function animationCompleted() {

				uploadInProgress = false;

				if ( file.filestatus == SWFUpload.FILE_STATUS.COMPLETE ) {

					$('#dialog').dialog('option', 'title', 'Upload Complete');
					$('#dialog').html('<strong>Your file, &quot;'+file.name+'&quot; was successfully uploaded.</strong><br /><br /><small>'+uploadCompleteText+'</small>');

				} else if ( file.filestatus == SWFUpload.FILE_STATUS.ERROR ) {

					$('#dialog').dialog('option', 'title', 'Failed Upload');
					$('#dialog').html('<strong>There was an error attempting to upload your file, &quot;'+file.name+'&quot;.</strong><br /><br /><small>Please reload the page and try again.</small>');

				} else {

					$('#dialog').dialog('option', 'title', 'Failed Upload');
					$('#dialog').html('<strong>There was an unknown error attempting to upload your file, &quot;'+file.name+'&quot;.</strong><br /><br /><small>Please reload the page and try again.</small>');

				}

	
				$('#dialog').dialog('option', 'buttons', {
					"Ok" : function() { 
						
						$(this).dialog('close'); 
						
						$("div#tabs").tabs('select', '#files');
						$("div#files strong.remove, div#all strong.remove").remove();
						$("div#files, div#all").append($('<br /><div class="file" href="'+savedFileName+'"><table cellpadding="0" cellspacing="0" valign="middle" align="left"><tr><td><a href="'+savedFileName+'" rel="facebox"><img src="'+savedFileName+'" border="0" /></a></td><td>&nbsp;&nbsp;'+file.name+'</td><td>&nbsp;&nbsp;<a href="#" class="assign-file" href="'+savedFileName+'">[Insert into Text]</a></td></tr></table></div>'));
						$("a[rel*=facebox]").facebox();
						
					} 
				});
				

			}
				
			var progress = $('#progressbar').progressbar('option', 'value');
			if ( progress < 100 ) {
				$('#progressbar div.ui-progressbar-value').animate({width:'100%'},1500,"linear",function() {
					animationCompleted();
				});
			} else animationCompleted();
			
		}
		
	}
	
	function uploadError(file, errorCode, message) {
		
		if ( console ) console.error(message);
		uploadInProgress = false;
		
		$('#dialog').html('<strong>There was an error (code:'+errorCode+') attempting to upload your file, &quot;'+file.name+'&quot;.</strong><br />Please reload the page and try again.');
		$('#dialog').dialog('option', 'buttons', {
			"Ok" : function() {
				$(this).dialog('close');
			}
		});
		
	}
		

	$(function() {
		
		swfu = new SWFUpload({
		
			upload_url : "<?=Template::controller_link("AssetsController", "upload/")?>",
			flash_url : "<?=swf_upload_path()?>",
			file_size_limit : fileSizeLimit,
			file_post_name : '<?=$data["file_post_name"]?>',
			post_params: {
				"assoc_table"	: "<?=$data['table']?>",
				"assoc_id"	: "<?=$data['id']?>",	
				"session_id" : "<?=session_id()?>"							
			},
			
			file_types : "<?=($data['allowed_file_types']!=''?$data['allowed_file_types']:'*.*')?>",
			file_types_description : "All Files",
			file_upload_limit : "0",
			file_queue_limit : "0",
			
			
			// Event Handler Settings - my functions for controll
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,

			// Button Settings
			button_text: '<span class="button">Select Files<\/span>',
			button_text_style: '.button { text-align: center; font-weight: bold; font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif; font-size: 11px; text-shadow: 0 1px 0 #FFFFFF; color:#464646; }',
			button_height: "23",
			button_width: "132",
			button_text_top_padding: 3,
			button_image_url: '<?=PUBLIC_URL?>images/upload-button-bg.png',
			button_placeholder_id : "uploadFilePlaceholder",
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND
						
		});

			
	});
	
</script>