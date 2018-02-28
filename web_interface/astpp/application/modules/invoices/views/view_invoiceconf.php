<? extend('master.php') ?>
<? startblock('extra_head') ?>

<?php endblock() ?>

<?php startblock('page-title') ?>
<?=$page_title?>
<?php endblock() ?>
<?php startblock('content')?>
<div class="container">
  <div class="row">
    <section class="slice color-three no-margin">
        <div class="w-section inverse no-padding">
            <?php echo $form; ?>
            <?php
			if(isset($validation_errors) && $validation_errors != ''){ ?>
                <script>
                var ERR_STR = '<?php echo $validation_errors; ?>';
                print_error(ERR_STR);
                </script>
            <?php } ?>
            </div>       
    </section>        
  </div>
</div>
<script type="text/javascript" src="js/jquery-1.8.0.min.js"></script>
<style>
#imagePreview {
    width: 264px;;
    height: 104px;
    background-position: right right;
    background-size: cover;
    -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
    display: inline-block;
	
}
#imagePreview:before {
	content: "×";
	float:right;
	margin-top:-10px;
	margin-left:150px;
	color:black;
	margin-right:-30px;
	cursor:pointer;
	font-size: 31px;
	font-weight: bold;
	display: inline-block;
	line-height: 0px;
	padding: 8px 3px 0px 5px;
	
	
}
#uploadFile{
 content: 'Select some files';
  display: inline-block;
 border: 1px solid #E4E4E4;
  border-radius: 3px;
  padding: 0px 0px;
  overflow-x:hidden;
  outline: none;
  white-space: nowrap;
  -webkit-user-select: none;
  cursor: pointer;
color: transparent;
background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
  font-weight: 400;
  font-size: 10pt;
}

</style>

<script type="text/javascript">

$( "#imagePreview" ).hide();

$(function() {
	$( "#logo_delete" ).live('click', function() {	
	var id =  $( "input[name=id]" ).val();
	var url='<?php echo base_url()."invoices/invoice_logo_delete/"; ?>'+id;
	var confirm_string = 'Are you sure want to remove logo?';
	var answer = confirm(confirm_string);
	if(answer){
		$.ajax({
			type:"POST",
			url:url,
			success: function(response){
				window.location = '<?php echo base_url()."invoices/invoice_conf/"; ?>';
			}
		});
	}else{
		return false;
	}
	});
	$( "#imagePreview" ).live('click', function() {	
		$( "#imagePreview" ).fadeOut();
		$( "#uploadFile" ).val('');
	});
	$("#uploadFile").on("change", function()
	{
		var files = !!this.files ? this.files : [];
		if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
		if (/^image/.test( files[0].type)){ // only image file
			var reader = new FileReader(); // instance of the FileReader
			reader.readAsDataURL(files[0]); // read the local file
	
			reader.onloadend = function(){ // set image data as background of div
				$("#imagePreview").show();
				//$("#imagePreview").css("height", "200px;");
				$("#imagePreview").css("background", "url("+this.result+") no-repeat left top");
				$("#imagePreview").css("background-size", "90% 60%");
				//background-size: 100% 100%;
			}
		}
	});
});
</script>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
