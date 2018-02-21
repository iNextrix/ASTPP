<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.7.1.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/facebox.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/flexigrid.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/flexigrid.css" type="text/css">
<link href="<?php echo base_url(); ?>assets/css/facebox.css" rel="stylesheet" media="all" />	

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("prefixes_grid","<?php echo base_url(); ?>accounts/customer_add_blockpatterns_json/<?= $accountid; ?>",<? echo $patters_grid_fields ?>,"");

        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
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
//                 confirm_string = 'Are you sure want to Add this Prefixis?';
//                 var answer = confirm(confirm_string);
//                 if(answer){
                    $.ajax({
                        type: "POST",
                        cache    : false,
                        async    : true,  
                        url: "<?= base_url(); ?>/accounts/customer_block_prefix/<?= $accountid ?>/",
                        data: "prefixies="+result,
                        success: function(data){ 
                        //alert(data);
                            if(data == 1)
                            {
                                $('#prefixes_grid').flexReload();
                                $('#pattern_grid').flexReload();
                            
                            } else{
                                alert("Problem In Add Patterns to account.");
                            }
                        }
                    });
//                 }
            } else{
                alert("Please select atleast one pattern.");
            }
        });        
        
    });
</script>
<div style="">
    <form action="" id="addlist_form" name="addlist_form" method="POST" enctype="multipart/form-data" style="display:block">
        <input type="hidden" id="add_patterns" name="add_patterns" readonly />
        <input type="button" id="add_patterns_btn"  class="ui-state-default float-right ui-corner-all ui-button" name="add_patterns_btn" value="Add To List">
    </form>
</div>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="padding-top: 35px;">  
    <div class="portlet-header ui-widget-header">Rates List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">        
            <table id="prefixes_grid" style="display:none;"></table>
        </form>
    </div>
</div>
