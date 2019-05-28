<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("product_cus_grid","",<? echo $grid_fields; ?>,<?php echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked')); 
        });
        $("#product_search_btn").click(function(){ 
            post_request_for_search("product_cus_grid","","product_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("product_cus_grid","");
        }); 

	$("#productlist").change(function(){ 
		
		$('#parent_product').attr('action', "<?php echo base_url();?>products/products_listing/");
		$('#parent_product').submit();
		
	});
    });
function optinproduct(id) {
	 var url="<?php echo base_url(); ?>products/products_optin/";

	  var status='false';
	  if($('#optin'+id).is(':checked')){
		status='true';
	  } 

	  $.ajax({
	      type:"POST",
	      url:url,
	      data:{product_id:id,status:status},
		success:function(data){ 
		$('#optin_admin_product').dialog('open');
		$('#optin_admin_product').modal('show');	
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
	<div class="w-section inverse p-0">
   	    <div class="col-12">
            	<div class="portlet-content mb-4"  id="search_bar" style="display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
    </div>
</section>


<?php if($accountinfo['type'] == 1){ ?>
<form  method="post" name="parent_product" id="parent_product" action="<?= base_url()."products/products_listing/";?>" >
<div class="card mb-4" id="floating-label">


	<div class="col-md-12">
		 <div class='col-md-4 py-4 px-0 float-right'>
		 	<select name="productlist" id="productlist" class="col-md-12 form-control selectpicker form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
		 		<option value='0' <?php  echo "selected"; ?>><?php echo gettext("My Product");?></option>
		 		<option value='1' ><?php echo gettext("Parent Product");?></option>
		 	</select>	      
		 </div>
	 </div>
 </div>

</form>

     

<?} ?>

<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
    	<div class="card col-md-12 pb-4">
        	       <table id="product_cus_grid" align="left" style="display:none;"></table>
        </div>
    </div>
</section>
<? endblock() ?>	
<? end_extend() ?>  
