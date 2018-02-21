<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("did_grid","",<? echo $grid_fields; ?>,"");
    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" >
    <div class="content-box-wrapper"> 
        <form method="post" action="<?= base_url() ?>user/user_dids_action/add/" enctype="multipart/form-data">
            <div class="sub-form">
                <div style="width:20%;">
                    <label class="desc">Available DIDs</label>
                    <? echo $didlist; ?>
                </div>
                <div>
                    <div style="margin-top:1px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Purchase DID" type="submit"></div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">DIDs List
        <span id="error_msg" class=" success"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="did_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<? endblock() ?>	

<? end_extend() ?>  
