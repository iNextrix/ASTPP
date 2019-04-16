<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
/********
ASTPP  3.0
Batch Delete
*******/
function search_btn(){
    document.getElementById('termination_rate_batch_dlt').style.display = 'block';
}
function check_btn(){
 	$.ajax({
		type: "POST",
		url: "<?= base_url()?>/rates/termination_rates_list_delete/",
		data:'',
		success:function(alt) {
		 if(alt > 0){
	  	   post_request_for_batch_delete("termination_rates_grid",alt,"termination_rate_search");
  	           $('#termination_rate_grid').flexOptions({newp:1}).flexReload();
		    location.reload();
		  }else{
		     alert('No record found');
		  }
		}
	});
}
        $("#termination_rate_batch_dlt").click(function(){
            post_request_for_batch_delete("termination_rate_grid","","termination_rate_search");
            $('#termination_rate_grid').flexOptions({newp:1}).flexReload();
        }); 
/******************/
    $(document).ready(function() {
        build_grid("termination_rate_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
        });
        $("#termination_rate_search_btn").click(function(){
            post_request_for_search("termination_rate_grid","","termination_rate_search");
        });        
        $("#id_reset").click(function(){
/*****
ASTPP  3.0 
Batch Delete 
*****/
    document.getElementById('termination_rate_batch_dlt').style.display = 'none';
/*******************/
            clear_search_request("termination_rate_grid","");
        });
        $("#batch_update_btn").click(function(){
            submit_form("termination_rate_batch_update");
        })
        $("#id_batch_reset").click(function(){ 
            $(".update_drp").each(function(){
                var inputid = this.name.split("[");
                $('#'+inputid[0]).hide();
            });
        });
        
       $(".update_drp").change(function(){
           var inputid = this.name.split("[");
           if(this.value != "1"){
               $('#'+inputid[0]).show();
           }else{
               $('#'+inputid[0]).hide();
           }
       }).each(function(){
            var inputid = this.name.split("[");
            if(this.value != "1"){
                $('#'+inputid[0]).show();
            }else{
                $('#'+inputid[0]).hide();
            }
        });
    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>     


<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>
<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
        <span id="error_msg" class=" success"></span>
            	<div class="portlet-content"  id="update_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_batch_update; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">
			<a href="https://starcompartners.com/termination#signup" target="_blank" class="col-xs-offset-9"><img title='Star Communication' alt='Star Communication' src="<?php echo base_url();?>/assets/images/logo-2.png"></a>
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="termination_rate_grid" align="left" style="display:none;"></table>
                        </form>
                        <div class="margin-t-10"></div>
                </div>  
            </div>
        </div>
    </div>
</section>


<? endblock() ?>	

<? end_extend() ?>  
