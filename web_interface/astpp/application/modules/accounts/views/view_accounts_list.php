<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("flex1","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
        });
        $("#account_search_btn").click(function(){
            post_request_for_search("flex1","","account_search");
        });        
        $("#id_reset").click(function(){ 
            clear_search_request("flex1","");
        });
         $("#batch_update_btn").click(function(){
            submit_form("reseller_batch_update");
        })
        
        $("#batch_update_btn").click(function(){
            submit_form("customer_batch_update");
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
    /**
ASTPP  3.0 
first used,creation,expiry search date picker
**/ 
     $(document).ready(function() {
	 $("#first_used").datetimepicker({
            timepicker:false,
            format:'Y-m-d',
            formatDate:'Y-m-d'});		
         $("#expiry").datetimepicker({
            timepicker:false,
            format:'Y-m-d',
            formatDate:'Y-m-d'
         });
         $("#creation").datetimepicker({
            timepicker:false,
            format:'Y-m-d',
            formatDate:'Y-m-d'
         });
    });
    /*********************************************************************/
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
   <?php /**
ASTPP  3.0 
first used,creation,expiry search date picker
**/ ?>
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
<?php /*********************************************/ ?>
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="flex1" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>



<? endblock() ?>	

<? end_extend() ?>  
