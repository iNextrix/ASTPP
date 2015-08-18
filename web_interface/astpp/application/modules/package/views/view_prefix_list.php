

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/facebox.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/flexigrid.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/flexigrid.css" type="text/css">
<link href="<?php echo base_url(); ?>assets/css/facebox.css" rel="stylesheet" media="all" />	

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("prefixes_grid","<?php echo base_url(); ?>package/customer_add_patterns_json/<?= $packageid; ?>",<? echo $patters_grid_fields ?>,"");

        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
	    $("#add_patterns_btn").removeAttr('disabled');
        });
        
        $("#add_patterns_btn").click(function(){
            var result = "";                        
            $(".chkRefNos").each( function () {
                if(this.checked == true) {     
                    result += ","+$(this).val();
                } 
            });     
            result = result.substr(1);
            if(result){
		$("#add_patterns_btn").attr('disabled','disabled');
                $.ajax({
                    type: "POST",
                    cache    : false,
                    async    : true,  
                    url: "<?= base_url(); ?>/package/customer_package_prefix/<?= $packageid ?>/",
                    data: "prefixies="+result,
                    success: function(data){ 
                        if(data == 1)
                        {
                            $('#prefixes_grid').flexReload();
                            $('#pattern_grid').flexReload();

                        } else{
                            alert("Problem In Add Patterns to account.");
                        }
                    }
                });
            } else{
                alert("Please select atleast one pattern.");
            }
        });        
        
    });
</script>

<section class="slice gray no-margin">

   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 margin-b-10">
	        <div class="col-md-10"><b><? echo "Rates List"; ?></b></div>
	  </div>

</div>
    </div>

</section><br/>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="padding-top: 35px;">  
 <!--   <div class="portlet-header ui-widget-header">Rates List<span class="ui-icon ui-icon-circle-arrow-s"></span></div> -->
    <div class="portlet-content">
    
<div style="width:690px;">
    <form action="" id="addlist_form" name="addlist_form" method="POST" enctype="multipart/form-data" style="display:block">
        <input type="hidden" id="add_patterns" name="add_patterns" readonly /><br/>
        <input type="button" id="add_patterns_btn"  class="btn btn-line-warning btn fa fa-plus-circle fa-lg" name="add_patterns_btn" value="Add To List">
    </form>
</div>
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">        
            <table id="prefixes_grid" style="display:none;"></table>
        </form>
    </div>
</div>


