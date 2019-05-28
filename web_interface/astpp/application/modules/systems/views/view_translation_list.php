<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("translation_cus_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); 
        });
        $("#translation_search_btn").click(function(){ 
            post_request_for_search("translation_cus_grid","","translation_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("translation_cus_grid","");
        }); 
    });
function optintranslation(id) {
	 var url="<?php echo base_url(); ?>systems/translation_optin/";

	  var status='false';

	  if($('#optin'+id).is(':checked')){
		status='true';
	  } 

	  $.ajax({
	      type:"POST",
	      url:url,
	      data:{translation_id:id,status:status},
		success:function(data){ 

		$('#optin_admin_translation').dialog('open');
		$('#optin_admin_translation').modal('show');		
		}
	  });
	}


</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?> 

<section class="slice color-three">
	<div class="w-section inverse no-padding">
   	    <div class="mb-4 col-12">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
    </div>
</section>
<?php if($accountinfo['type'] == 1){ ?>
<form  method="post" name="test" id="test" action="<?= base_url()."systems/translation_list/";?>" >
<input type = "hidden" name ="id" value = '1' >
 <input type= "submit" class = "btn btn-line-parrot btn-lg" id ="translation_purchase" value= "Parent translation" >

</form>
    

<?} ?>

<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
    	<div class="card col-md-12 pb-4">
        	       <table id="translation_cus_grid" align="left" style="display:none;"></table>
        </div>
    </div>
</section>
<? endblock() ?>	
<? end_extend() ?>  
