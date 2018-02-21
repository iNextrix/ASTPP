<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
	
	$("#updatebar").click(function(){
             $("#update_bar").toggle();
      	});      

        build_grid("inbound_rates_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
        });
        $("#inbound_search_btn").click(function(){
            post_request_for_search("inbound_rates_grid","","inbound_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("inbound_rates_grid","");
        });

         $("#batch_update").click(function(){
            submit_form("inbound_batch_update");
        })
        $("#id_batch_reset").click(function(){ 
            $(".update_drp").each(function(){
                var inputid = this.name.split("[");
                $('#'+inputid[0]).hide();
            });
        });
        
       $(".update_drp").change(function(){
           var inputid = this.name.split("[");
           if(this.value != "1"){
               $('#'+inputid[0]).show();
           }else{
               $('#'+inputid[0]).hide();
           }
       }).each(function(){
            var inputid = this.name.split("[");
            if(this.value != "1"){
                $('#'+inputid[0]).show();
            }else{
                $('#'+inputid[0]).hide();
            }
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
        <?php echo $form_search; ?>
    </div>
</div>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
    <div class="portlet-header ui-widget-header" ><span id="updatebar" style="cursor:pointer">Batch Update
        <span id="error_msg" class=" success"></span>
        </span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content"  id="update_bar" style="cursor:pointer; display:none">
        <?php echo $form_batch_update; ?>
    </div>
</div>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Origination Rates List
        <span id="error_msg" class=" success"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="inbound_rates_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<? endblock() ?>	
<? end_extend() ?>  
