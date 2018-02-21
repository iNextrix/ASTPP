<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("flex1","",<? echo json_encode($grid_fields) ?>,buttons);
        
        $("#callingcard_search").click(function(){
            post_request_for_search("flex1","","search_form1");
        });        
        $("#id_reset").click(function(){
            clear_search_request("flex1","");
        });
        
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
    <?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">
    <div class="portlet-header ui-widget-header" ><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <?php $this->load->view("view_callingcards_list_search"); ?>
</div>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Cards List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="flex1" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  
<div class="ui-overlay" id="overlay" style="display:none;"><div class="ui-widget-overlay"></div>
    <div class="ui-widget-shadow ui-corner-all" style="width: 302px; height: 152px; position: absolute; left: 50px; top: 30px;">
        <div style="width: auto; height: auto;margin:0 auto; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all">
            <div class="ui-dialog-content ui-widget-content" style="background: none; border: 0;">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            </div>
        </div>
    </div>
</div>  
<? endblock() ?>	

<? end_extend() ?>  
