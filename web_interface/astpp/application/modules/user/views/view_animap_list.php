<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("animap_grid","",<? echo $grid_fields; ?>,"");
    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" >
    <div class="content-box-wrapper"> 
        <div class="sub-form">   
            <form method="post" action="<?= base_url() ?>user/user_animap_action/add/" enctype="multipart/form-data">
                <div><label class="desc">ANI</label><input class="text field large" name="ANI" size="20" type="text"></div>
                <div><label class="desc">Context</label><input class="text field large" name="context" size="20" type="text"></div>
                <div style="margin-top:14px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Map ANI" type="submit"></div>
            </form>  
        </div>
    </div>
</div>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">ANI MAP List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="animap_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<? endblock() ?>	

<? end_extend() ?>  
