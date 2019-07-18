<?php include(FCPATH.'application/views/popup_header.php'); ?>




<script type="text/javascript" language="javascript">
    $(document).ready(function() {
       $('a[rel*=facebox]').facebox();
       build_grid("prefixes_grid","<?php echo base_url(); ?>accounts/customer_add_blockpatterns_json/<?= $accountid; ?>",<? echo $patters_grid_fields ?>,"");

       $('.checking').click(function () {
          $('.PatternChkBox').prop('checked', $(this).prop('checked'));
           $("#add_patterns_btn").removeAttr('disabled');     
       });
   });
    
    function add_package_pattern(){ 
       var result = "";                        
       $(".PatternChkBox").each( function () {
        if(this.checked == true) {     
            result += ","+$(this).val();
        } 
    });     
       result = result.substr(1);
       if(result){
        $.ajax({
            type: "POST",
            cache    : false,
            async    : true,  
            url: "<?= base_url(); ?>/accounts/customer_block_prefix/<?= $accountid ?>/",
            data: "prefixies="+result,
            success: function(data){ 
                if(data)
                {
                    $('.checkall').attr('checked', false);
                    $('#prefixes_grid').flexReload();
                    $('#pattern_grid').flexReload();
                    
                } else{
                    alert("<?php echo gettext('Problem In Add Patterns to account.'); ?>");
                }
            }
        });
    } else{
        alert("<?php echo gettext('Please select atleast one pattern.'); ?>");
    }
}
</script>



<section class="slice m-0">
	<div class="w-section inverse p-0">
		<div>
			<div>
				<div class="col-md-12 p-0 card-header">
					<h3 class="fw4 p-4 m-0"><? echo gettext("Blocked Codes"); ?></h3>
				</div>
			</div>
		</div>
	</div>
</section>


<section class="slice m-0">
	<div class="w-section inverse p-4">

		<div class="col-12 pb-4">
			<form action="" id="addlist_form" name="addlist_form" method="POST"
				enctype="multipart/form-data" style="display: block">
				<input type="hidden" id="add_patterns" name="add_patterns" readonly />
				<button id="add_patterns_btn" class="btn btn-line-warning btn"
					name="add_patterns_btn" onclick="add_package_pattern();">
					<i class="fa fa-plus-circle fa-lg"></i><?php echo gettext("Add To List");?></button>
			</form>
		</div>


		<div class="pop_md col-12 pb-4">
			<div class="card">
				<div class="col-12">
					<ul class="p-0">
						<div class="col-12" id="floating-label">
							<h3 class="bg-secondary text-light p-3 rounded-top"><? echo gettext("Blocked Codes"); ?></h3>

							<div class="col-12" id="package_patterns">

								<table id="prefixes_grid" style="display: none;"></table>

							</div>
						</div>
					</ul>
				</div>
			</div>

</section>





