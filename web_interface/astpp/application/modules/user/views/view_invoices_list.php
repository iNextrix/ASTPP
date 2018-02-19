<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("invoice_grid","",<? echo $grid_fields; ?>,"");
        $("#invoice_search_btn").click(function(){
            post_request_for_search("invoice_grid","","invoice_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("invoice_grid","");
        });
            jQuery("#date").datetimepicker({format:'Y-m-d'});	
        jQuery("#invoice_date").datetimepicker({format:'Y-m-d'});	
    });
</script>
	
<? endblock() ?>

<? startblock('page-title') ?>
    <?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        
<div id="main-wrapper" class="tabcontents">  
    <div id="content">   
        <div class="row"> 
            <div class="col-md-12 no-padding color-three border_box"> 
                <div class="pull-left">
                    <ul class="breadcrumb">
                        <li><a href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= ucfirst($accounttype); ?>s</a></li>
                        <li>
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"><?= ucfirst($accounttype); ?> Profile </a>
                        </li>
                        <li class="active">
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_invoices/".$edit_id."/"; ?>">
                                Invoices
                            </a>
                        </li>
                    </ul>
                </div>
            </div>     
            <div class="padding-15 col-md-12">
                <div class="col-md-12 no-padding">
                    <div id="show_search" class="pull-right margin-t-10 col-md-4 no-padding">
                        <div id="show_search"class="pull-right"><i class="fa fa-search"></i> Search</div>
                    </div>
                </div> 
                <div class="col-md-12 no-padding">
                    <div class="col-md-12 color-three padding-b-20 slice color-three pull-left content_border">
                        <table id="invoice_grid" align="left" style="display:none;"></table>
                    </div>   
                </div>
            </div>
        </div>
    </div>
</div>
  
<? endblock() ?>	
<? end_extend() ?>  
