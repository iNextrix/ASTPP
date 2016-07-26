<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("opensipsdevice_grid","",<? echo $grid_fields; ?>,<? echo $fs_grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked);
        });
        $("#opensipsdevice_search_btn").click(function(){
            post_request_for_search("opensipsdevice_grid","","opensips_list_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("opensipsdevice_grid","");
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

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="opensipsdevice_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div><br/>
    </div>
</section>

<? endblock() ?>	
<? end_extend() ?>  
