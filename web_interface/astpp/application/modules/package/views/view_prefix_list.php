


<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/flexigrid.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/flexigrid.css" type="text/css">
<link href="<?php echo base_url(); ?>assets/css/facebox.css" rel="stylesheet" media="all" />	

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("prefixes_grid","<?php echo base_url(); ?>package/package_patterns_add_json/<?= $packageid; ?>",<? echo $patters_grid_fields ?>,"");

        $('.checking').click(function () {
			$('.PatternChkBox').attr('checked', this.checked);//if you want to select/deselect checkboxes use this
			$("#add_patterns_btn").removeAttr('disabled');
        });   
        $("#left_panel_prefix_quick_search").keyup(function() {
            quick_search("package/package_prefix_quick_search/", "left_panel_prefix_quick_search");
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
	        <div class="col-md-10"><b><? echo "Rates List"; ?></b></div>
	  </div>
</div>
</div>
    </div>

</section>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">  
 <!--   <div class="portlet-header ui-widget-header">Rates List<span class="ui-icon ui-icon-circle-arrow-s"></span></div> -->
    <div class="portlet-content">
        <div class="row">
            <div class="padding-15 col-md-12">
                <div class="col-md-12 no-padding">
                    <div class="pull-left margin-t-10">
                        <form action="" id="addlist_form" name="addlist_form" method="POST" enctype="multipart/form-data" style="display:block">
                            <input type="hidden" id="add_patterns" name="add_patterns" readonly />
                            <button id="add_patterns_btn"  class="btn btn-line-warning btn" name="add_patterns_btn" onclick="add_package_pattern();"><i class="fa fa-plus-circle fa-lg"></i>Add To List</button>
                        </form>
                    </div>
                    <div id="show_search" class="pull-right margin-t-10 col-md-4 no-padding">
                        <input type="text" name="origination_rate_list_search" id="left_panel_prefix_quick_search" class="col-md-5 form-control pull-right" value="<?php
                            $search_data = $this->session->userdata('origination_rate_list_search');
                            if ( is_array($search_data) && !empty($search_data['pattern']) && !empty($search_data['pattern']['pattern-string']) ) {
                                echo $search_data['pattern']['pattern-string'];
                            } else {
                                echo "";
                            }
                            ?>" placeholder="Search"/>
                    </div>
                </div>
            </div>
        </div>
            <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                <table id="prefixes_grid" style="display:none;"></table>
            </form>
    </div>
</div>


