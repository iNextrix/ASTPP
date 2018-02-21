<? extend('master.php') ?>
<? startblock('extra_head') ?>	   

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        
        build_grid("pattern_grid","<?php echo base_url(); ?>package/package_pattern_json/<?= $package_id; ?>",<? echo $pattern_grid_fields ?>, <?= $pattern_grid_buttons ?>);
    });
</script>
<? endblock() ?>
<? startblock('content') ?>

<ul class="tabs" data-persist="true">
        <li><a href="#package_details">Package Details</a></li>
        <li><a href="#package_patterns">Package Patterns</a></li>
</ul>

<div class="tabcontents">        
      <div>
          <div class="col-md-12">
            <section class="slice color-three no-margin">
                <div class="w-section inverse no-padding">
            <div id="package_details">
                  <?php echo $form; ?>
                   <?php
                        if(isset($validation_errors) && $validation_errors != ''){ ?>
                            <script>
                                var ERR_STR = '<?php echo $validation_errors; ?>';
                                print_error(ERR_STR);
                            </script>
                  <? } ?>
            </div>  

         <div id="package_patterns">
                    <div class="col-md-12 color-three padding-b-20">
                          <table id="pattern_grid" align="left" style="display:none;"></table>  
                    </div>
        </div>      
        </div>
    </section>        
  </div>
</div>    
</div>

<!--
							<div id="package_patterns">
								<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
								 	<div class="portlet-header ui-widget-header">
									   <span class="ui-icon ui-icon-circle-arrow-s"></span>
									</div>
									<div style="color:red;">
									     <table id="pattern_grid" align="left" style="display:none;"></table>
									</div>
								  </div>      
							</div>
    		</div>
		</div>    -->  
            
 <!--   </div>
-->
<? endblock() ?>

<? startblock('sidebar') ?>
Filter by
<? endblock() ?>

<? end_extend() ?>  
