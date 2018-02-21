<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("callingcard_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        
        $("#callingcard_search_btn").click(function(){
            post_request_for_search("callingcard_grid","","callingcard_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("callingcard_grid","");
        });
        
    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
    <?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">
    <div class="portlet-header ui-widget-header" ><span id="show_search" style="cursor:pointer">Search</span>
        <span id="active_search"  style="margin-left:10px; text-align: center;background-color: none;color:#1c8400;"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
   <div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
    <?php echo $form_search;?>
    </div>
</div>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Calling Card List
        <span id="error_msg" class=" success"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="callingcard_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  
  
<? endblock() ?>	
<? end_extend() ?>  
