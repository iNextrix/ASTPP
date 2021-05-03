<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/js/flexigrid.js"></script>
<link rel="stylesheet"
	href="<?php echo base_url(); ?>assets/css/flexigrid.css"
	type="text/css">
<link href="<?php echo base_url(); ?>assets/css/facebox.css"
	rel="stylesheet" media="all" />
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("prefixes_grid","<?php echo base_url(); ?>package/package_patterns_add_json/<?= $packageid; ?>",<? echo $patters_grid_fields ?>,"");
        $('.checking').click(function () {
			$('.PatternChkBox').attr('checked', this.checked);
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
                    type	: "POST",
                    url		: "<?= base_url(); ?>/package/package_patterns_add_info/<?= $packageid ?>/",  
                    data	: "prefixies="+result,
                    success : function(data){
                        if(data)
                        {
							$('.checkall').attr('checked', false);
                            $('#prefixes_grid').flexReload();
                            $('#package_pattern_list').flexReload();

                        } else{
                            alert("Problem In Add Patterns to account.");
                        }
                    }
                });
            } else{
                alert("Please select atleast one pattern.");
            }		
	}
</script>

<section class="slice gray no-margin">
	<div class="w-section inverse no-padding">
		<div>
			<div>
				<div class="col-md-12 no-padding margin-t-15 margin-b-10">
					<div class="col-md-10">
						<b><? echo gettext ( "Rates List"); ?></b>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>

<div
	class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
	<div class="portlet-content">

		<div style="width: 690px;">
			<form action="" id="addlist_form" name="addlist_form" method="POST"
				enctype="multipart/form-data" style="display: block">
				<input type="hidden" id="add_patterns" name="add_patterns" readonly /><br />
				<button id="add_patterns_btn" class="btn btn-line-warning btn"
					name="add_patterns_btn" onclick="add_package_pattern();">
					<i class="fa fa-plus-circle fa-lg"></i><?php echo gettext("Add To List"); ?></button>
			</form>
		</div>
		<form method="POST" action="del/0/" enctype="multipart/form-data"
			id="ListForm">
			<table id="prefixes_grid" style="display: none;"></table>
		</form>
	</div>
</div>


