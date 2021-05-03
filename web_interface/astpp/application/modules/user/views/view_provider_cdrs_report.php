<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('.rm-col-md-12').addClass('float-right');
        $(".rm-col-md-12").removeClass("col-md-12");
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear() + "-"
            + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
                + ("0" + currentdate.getDate()).slice(-2) + " 00:00:00";
            
        var datetime1 = currentdate.getFullYear() + "-"
           +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
            + ("0" + currentdate.getDate()).slice(-2) + " 23:59:59";

        
        $("#customer_cdr_from_date").datetimepicker({
             value:datetime,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         });  
         $("#customer_cdr_to_date").datetimepicker({
             value:datetime1,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         });
        
        build_grid("user_provider_cdrs_report","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $("#user_cdr_search_btn").click(function(){
            post_request_for_search("user_provider_cdrs_report","","user_provider_cdrs_report_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("user_provider_cdrs_report","");
        });
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        

<section class="slice color-three">
	<div class="w-section inverse no-padding mb-4">
    	<div class="col-md-12 ">
   	    <div class="row " >
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>

<section class="slice color-three px-2">
	<div class="w-section inverse p-0">
                <div class="card col-md-12 pb-4">      
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="user_provider_cdrs_report" align="left" style="display:none;"></table>
                        </form>
                </div>  
    </div>
</section>


<? endblock() ?>	
<? end_extend() ?>  
