<script type="text/javascript">

	fileSizeLimit = "<?=$data['filesize_limit']?>";
	uploadCompleteText = "<?=($data['uploadCompleteText']!=''?$data['uploadCompleteText']:'You can now save your text changes and continue.')?>";
	
	function fileDialogComplete(numFilesSelected, numFilesQueued) {
		try {
			if (numFilesQueued > 0) {
				
				<? if ( !$data['visible'] ) : ?>
				
				var div = $('<p>For technical reasons, we require you to first create your text and then upload.<br /><br />Clicking &quot;Ok&quot; below will save your work and reload the page for you. Then you will be able select your file to upload.</p>').appendTo($('body'));
				div.dialog({
					bgiframe: true,
					modal: true,
					resizable:false,
					title : 'Must Refresh Page',
					buttons : {
						"Ok" : function() { 
							$(this).dialog('close');
							$('#profile_submit').click();
						}
					}
				});
				
				<? else : ?>
			
				this.startUpload();
				
				<? endif ?>
				
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

				<? if ( $data['customCompleteFunction'] != '' ): ?>
				
				<? echo $data['customCompleteFunction'] ?>(file);
				
				<? else : ?>

				$('#dialog').dialog('option', 'buttons', {
					"Ok" : function() { 
						$(this).dialog('close'); 
						if ( file.filestatus == SWFUpload.FILE_STATUS.COMPLETE ) {
	
							var aLink = $('a[title=Download File]');
							if ( aLink.length ) {
								
								aLink.css('color', '#ffffff')
								.animate({
									backgroundColor: "#FF9900"
								}, "fast", function() {
									$(this).css('color', '#333333')
									.animate({
										backgroundColor: "#E6E6E6"
									}, "fast");
								}).attr('href', savedFileName);
								
							} else {
								
								$(' <a target="_self" title="Download File" href="'+savedFileName+'">Download</a>')
									.insertAfter($('strong:contains(File:)'))
									.css('color', '#ffffff')
									.animate({
										backgroundColor:'#FF9900'
									}, 'fast', function() {
										$(this).css('color', '#333333')
										.animate({
											backgroundColor: '#E6E6E6'
										}, 'fast');
									}).attr('href', savedFileName);
							}
						}
					} 
				});
				
				<? endif; ?>

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
		
	<? if ( $data['visible'] ) : ?>

	$(function() {
		
		swfu = new SWFUpload({
		
			upload_url : "<?=UPLOAD_URL?>pull.php?",
			flash_url : "<?=swf_upload_path()?>",
			file_size_limit : fileSizeLimit,
			file_post_name : '<?=$data["file_post_name"]?>',
			post_params: {
				"key" 	: "<?=$data['key']?>",
				"<?=$data['key']?>"	: "<?=$data['id']?>",
				"table"	: "<?=$data['table']?>",	
				"store_filesize" : "<?=$data['store_filesize']?>",
				"store_file_type" : "<?=$data['store_filetype']?>",
				"username" : "<?=Input::session("fullname")?>",
				"session_id" : "<?=session_id()?>"
				
				<? if ( isset($data['save_as_image']) && $data['save_as_image'] === true ) : ?>
				,	// Super important!
					"save_as_image" : "true",
					<? if ( isset($data['resize_image']) ) : ?>
					"resize_image" : "<?=$data['resize_image']?>",
					"image_width" : "<?=$data['image_width']?>",
					"image_height" : "<?=$data['image_height']?>"
					<? endif ?>
				<? endif ?>
							
			},
			
			file_types : "<?=($data['allowed_file_types']!=''?$data['allowed_file_types']:'*.*')?>",
			file_types_description : "All Files",
			file_upload_limit : "0",
			file_queue_limit : "<?=($data['file_queue_limit']!=''?$data['file_queue_limit']:'1')?>",
			
			
			// Event Handler Settings - my functions for controll
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,

			// Button Settings
			button_image_url : "<?=PUBLIC_URL?>images/up_alt.png",
			button_placeholder_id : "uploadFilePlaceholder",
			button_width: 180,
			button_height: 40,
			button_text : '<span class="button">Select a File<span class="buttonSmall">(<?=$data["filesize_limit"]?> Max)</span></span>',
			button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
			button_text_top_padding: 12,
			button_text_left_padding: 32,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND
						
		});

			
	});
	
	<? endif ?>

</script>