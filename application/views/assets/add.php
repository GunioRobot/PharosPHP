
	<script type="text/javascript">
		$(function() { 
			$("#tabs").tabs();
		});
	</script>
	

	<div id="tabs">
		
		<ul>
			<li class="tab"><a href="#default" title="Upload">Upload</a></li>
			<li class="tab"><a href="#files" title="Choose Existing">Gallery</a></li>
			<li class="tab"><a href="#all" title="Browse All">Library</a></li>
		</ul>
		
		<div id="default">

			<h3>Add Media File</h3><hr />
			<p>Choose a file from your computer to upload and insert into the this post.</p>

			<form action="<?=Template::controller_link("AssetsController", "upload/")?>" method="post" enctype="multipart/form-data">
				<input type="submit" name="save" value="" class="hidden" />
				<input type="hidden" name="id" id="id" value="<?=$id?>" />
				<input type="hidden" name="table" id="table" value="<?=$table?>" />

				<div id="flash-upload-ui">
					<div>

						<table cellpadding="0" cellspacing="0">
							<tr>
								<td>Choose files to upload:&nbsp;</td>
								<td><span id="uploadFilePlaceholder">boo</span></td>
							</tr>
						</table>

					</div>
				</div>

			</form>

		</div>

		<div id="files">

			<h3>Add Media File</h3><hr />
			<p>Choose an existing file to insert into the this post.  <br /><em>Files shown here have been uploaded directly to this post.</em></p>

			
			<? if ( !empty($data) ): foreach($data as $file) : ?>
			<br /><div class="file" href="<?=UPLOAD_URL.$file->filename?>">
				<table cellpadding="0" cellspacing="0" valign="middle" align="left">
					<tr>
						<td>
							<a href="<?=UPLOAD_URL.$file->filename?>" class="facebox" rel="facebox"><img src="<?=UPLOAD_URL.$file->filename?>" border="0" /></a>
						</td>
						<td>
							&nbsp;&nbsp;<?=$file->title?>
						</td>
						<td>
							&nbsp;&nbsp;<a href="#" class="assign-file" href="<?=UPLOAD_URL.$file->filename?>">[Insert into Text]</a>
						</td>
					</tr>
				</table>
			</div>
			<? endforeach; else: ?>
			<br /><strong class="remove">Oops, you haven't uploaded any images to this post yet.</strong>
			<? endif; ?>

		</div>
		
		<div id="all">

			<h3>Add Media File</h3><hr />
			<p>Choose an existing file to insert into the this post.  <br /><em>Files shown here are in the global library.</em></p>

			<? if ( !empty($all) ): foreach($all as $file) : ?>
			<br /><div class="file" href="<?=UPLOAD_URL.$file->filename?>">
				<table cellpadding="0" cellspacing="0" valign="middle" align="left">
					<tr>
						<td>
							<a href="<?=UPLOAD_URL.$file->filename?>" class="facebox" rel="facebox"><img src="<?=UPLOAD_URL.$file->filename?>" border="0" /></a>
						</td>
						<td>
							&nbsp;&nbsp;<?=$file->title?>
						</td>
						<td>
							&nbsp;&nbsp;<a href="#" class="assign-file" href="<?=UPLOAD_URL.$file->filename?>">[Insert into Text]</a>
						</td>
					</tr>
				</table>
			</div>
			<? endforeach; else: ?>
			<br /><strong class="remove">Oops, there aren't any images in the system yet.</strong>
			<? endif; ?>

		</div>		
		
	</div>
	
		

	