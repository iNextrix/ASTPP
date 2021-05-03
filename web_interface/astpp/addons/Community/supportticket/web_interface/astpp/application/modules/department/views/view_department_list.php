<? extend('master.php') ?>
<? startblock('extra_head') ?>


<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("department_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked'));
        });
        $("#department_search_btn").click(function(){
            
            post_request_for_search("department_grid","","department_search");
        });        
        $("#id_reset").click(function(){ 
            clear_search_request("department_grid","");
        });
        
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        
<style>	
	
.pure-button-primary {
/*	color:blue;height:25px;width:150px;background-color: rgb(0, 120, 231);color:white;*/
	width:140px;color:#fff; background-color:#79C447; border-radius:4px; font-family:arial; text-align:center;box-shadow:0px 1px 1px #406826;padding:5px 5px 5px 5px;border:2px #63a139;cursor:pointer;font-family: 'Lato', sans-serif;
}
</style>

<section class="slice color-three">
    <div class="w-section inverse p-0">
        <div class="col-12">
                <div class="portlet-content mb-4"  id="search_bar" style="display:none;">
                        <?php echo $form_search; ?>
                </div>
            </div>
    </div>
</section>
 

<section class="slice color-three">
	<div class="w-section inverse p-0">
    	<div class="card col-md-12 pb-4">  
			<form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
				<table id="department_grid" align="left" style="display:none;"></table>
			</form>
		</div>
	</div>
</section> 
 

<? endblock() ?>	

<? end_extend() ?>  
 
 
