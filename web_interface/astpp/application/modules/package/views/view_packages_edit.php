<? extend('master.php') ?>
<? startblock('extra_head') ?>	   
<script type="text/javascript" src="<?= base_url() ?>js/ui/ui.tabs.js"></script>
<? startblock('page-title') ?>
<?= $page_title ?><br/>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#tabs').tabs();
        build_grid("pattern_grid","<?php echo base_url(); ?>package/package_pattern_json/<?= $package_id; ?>",<? echo $pattern_grid_fields ?>, <?= $pattern_grid_buttons ?>);
    });
</script>
<? endblock() ?>
<? startblock('content') ?>
<div id="tabs">
    <ul>
        <li><a href="#package_details">Package Details</a></li>
        <li><a href="#package_patterns">Package Patterns</a></li>
    </ul>	
    <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
<!--         <div class="portlet-header ui-widget-header">Edit Package<span class="ui-icon ui-icon-circle-arrow-s"></span></div> -->
        <div class="portlet-content" id="package_details">
            <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
                <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
                    <?= @$page_title ?>
                    <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                <div style="color:red;margin-left: 60px;">
                    <?php
                    if(isset($validation_errors)){
                        $data_errrors = json_decode($validation_errors);
                        foreach ($data_errrors as $key => $value) {
                            echo $value . "<br/>";
                        }
                    }
                    ?> 
                </div>
                <?php echo $form; ?>
            </div>      
        </div>

        <div id="package_patterns">
            <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
                <div class="portlet-header ui-widget-header">
                    Package Pattern List<span class="ui-icon ui-icon-circle-arrow-s"></span>
                </div>
                <div style="color:red;">
                     <table id="pattern_grid" align="left" style="display:none;"></table>
                </div>
            </div>      
        </div>
    </div>
</div>
<? endblock() ?>

<? startblock('sidebar') ?>
Filter by
<? endblock() ?>

<? end_extend() ?>  
