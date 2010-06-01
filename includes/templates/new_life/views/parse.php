
	<div class="titleTabs">
		<h1><?=$this->title?></h1>
		<br clear="all" />
	</div>
	<div class="contentTitleBar titleBar"><b class="btop"></b>
		<div></div>
	</div>
	<div class="nonTableContent">
	 	<form id="profile" action="<?=controller_link(get_class($this), "parse/")?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="upload_completed" id="upload_completed" value="false" />
			<div class="col3col">
				
				<p>This CMS tool provides a simple way to import product &amp; category information from the Excel database you are providing.</p>
				<p>You will be able to review all changes and make corrections after this procedure.</p>
				<br />
				
				<p><strong>This operation may take several minutes.  Please wait patiently.</strong></p><br />
				
				<div class="floatLeft"><p class="singleText"><strong>Excel Database File (.xls/.xlsx):</strong><br />
					<span id="uploadFilePlaceholder"></span>
				</p><br /></div>
				<div class="clearBoth"></div>	    
				
			</div>
			<div class="clearBoth"></div><br />
		</form>
	</div>
	<div class="contentBottomBar"><b class="bbot"><b></b></b></div>
	<div class="floatLeft" align="center"><a href="<?=controller_link(get_class($this), "parse/")?>" id="parse-xml" class="buttons disabled">Parse Now</a></div>
	<br clear="all" />