<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $(this).css('background-color', 'Green');
        build_grid("customersummary_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        
        $("#customersummary_search_btn").click(function(){
            document.customersummary_search.submit();
        });
        $("#id_reset").click(function(){
            clear_search_request("customersummary_grid","");
            window.location="<? echo base_url() ?>summary/customer_clearsearchfilter/";
        });
    });
</script>
<script>
    $(document).ready(function() {
        jQuery("#customer_from_date").datetimepicker({format:'Y-m-d H:i:s'});		
        jQuery("#customer_to_date").datetimepicker({format:'Y-m-d H:i:s'});
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
                    <form action="<?php echo base_url(); ?>/summary/customer_search/" method="post" accept-charset="utf-8" id="customersummary_search" name="customersummary_search">
                        <ul class='padding-16'>
                            <fieldset>
                                <legend><b>Search</b></legend>
                                <div class="col-md-4 no-padding">
                                    <label class="search_label col-md-12 no-padding">From Date</label>
                                    <input type="text" name="callstart[]" value="<?php echo isset($session_info['callstart'][0]) ? $session_info['callstart'][0] : date("Y-m-d") . " 00:00:00"; ?>" id="customer_from_date" size="20" class="col-md-5 form-control text field "  />
                                </div>
                                <div class="col-md-4 no-padding">
                                    <label class="search_label col-md-12 no-padding">To Date</label>
                                    <input type="text" name="callstart[]" value="<?php echo isset($session_info['callstart'][1]) ? $session_info['callstart'][1] : date("Y-m-d") . " 23:59:59"; ?>" id="customer_to_date" size="20" class="col-md-5 form-control text field "  />
                                </div>
                                <div class="col-md-4 no-padding">
                                    <label class="search_label col-md-12 no-padding">Account</label>

                                    <select name="accountid" class='col-md-5 form-control selectpicker' data-live-search='true'>
                                        <option value=''>--Select--</option>
                                        <?php if (!empty($accountlist)) {
											foreach ($accountlist as $key => $value) {
												?>
                                                <optgroup label="<?php echo $key ?>">
                                                    <?php
													foreach ($value as $sub_key => $sub_value) {
														$selected = null;
														if (isset($session_info['accountid']) && $session_info['accountid'] > 0 && $sub_key == $session_info['accountid']) {
															$selected = "selected";
														}
														?>
                                                        <option value='<?php echo $sub_key; ?>'<?php echo $selected; ?>><?php echo $sub_value ?></option>
                                                <? }
												?>
                                                </optgroup>
    <? }
}
?>
                                    </select>
                                </div>
                                <div class="col-md-4 no-padding">
                                    <label class="search_label col-md-12 no-padding">Code </label>
                                    <input type="text" name="pattern[pattern]" value="<?php echo (isset($session_info['pattern']) && isset($session_info['pattern']['pattern']) && !empty($session_info['pattern']['pattern'])) ? $session_info['pattern']['pattern'] : ''; ?>" size="20" maxlength="15" class="col-md-5 form-control text field "/>
                                    <select name="pattern[pattern-string]" class='col-md-5 form-control selectpicker' style='margin-left:5px;' data-live-search='true'>
                                        <?php
										if (!empty($search_string_type)) {
											foreach ($search_string_type as $key => $value) {
												$selected = null;
												if (isset($session_info['pattern']) && isset($session_info['pattern']['pattern']) && !empty($session_info['pattern']['pattern']) && $session_info['pattern']['pattern-string'] == $key) {
													$selected = "selected";
												}
												?>
                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value ?></option>
    <?php }
}
?>       
                                    </select>
                                </div>
                                <div class="col-md-4 no-padding">
                                    <label class="search_label col-md-12 no-padding">Destination </label>
                                    <input type="text" name="notes[notes]" value="<?php echo (isset($session_info['notes']) && isset($session_info['notes']['notes']) && !empty($session_info['notes']['notes'])) ? $session_info['notes']['notes'] : ''; ?>" size="20" class="col-md-5 form-control text field "  />
                                    <select name="notes[notes-string]" class='col-md-5 form-control selectpicker' style='margin-left:5px;' data-live-search='true'>
                                        <?php
										if (!empty($search_string_type)) {
											foreach ($search_string_type as $key => $value) {
												$selected = null;
												if (isset($session_info['notes']) && isset($session_info['notes']['notes']) && !empty($session_info['notes']['notes']) && $session_info['notes']['notes-string'] == $key) {
													$selected = "selected";
												}
												?>
                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value ?></option>
    <?php }
}
?>       
                                    </select>
                                </div>
                                <div class="col-md-4 no-padding">
                                    <input type="hidden" name="ajax_search" value="1" />
                                </div>
                                <div class="col-md-4 no-padding">
                                    <input type="hidden" name="advance_search" value="1" />
                                </div>
                            </fieldset>
                        </ul>
                        <ul class='padding-16'>
                            <fieldset>
                                <legend><b>Group By</b></legend>
                                 <div class="col-md-3 no-padding">
                                    <label class="search_label col-md-12 no-padding">Group By #Time</label>
                                    <select name="time" class='col-md-5 form-control selectpicker' style='margin-left:5px;' data-live-search='true'>
                                        <?php
										if (!empty($groupby_time)) {
											foreach ($groupby_time as $key => $value) {
												$selected = null;
												if (isset($session_info['time']) && !empty($session_info['time']) && $session_info['time'] == $key) {
													$selected = "selected";
												}
												?>
                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value ?></option>
                                            <?php }
										}
										?>  
                                    </select>
                                 </div>
                                <div class="col-md-3 no-padding">
                                    <label class="search_label col-md-12 no-padding">Group By #1</label>
                                    <select name="groupby_1" class='col-md-5 form-control selectpicker' style='margin-left:5px;' data-live-search='true'>
                                        <?php
										if (!empty($groupby_field)) {
											foreach ($groupby_field as $key => $value) {
												$selected = null;
												if (isset($session_info['groupby_1']) && !empty($session_info['groupby_1']) && $session_info['groupby_1'] == $key) {
													$selected = "selected";
												}
												?>
                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value ?></option>
                                            <?php }
										}
										?>        
                                    </select>
                                </div>
                                <div class="col-md-3 no-padding">
                                    <label class="search_label col-md-12 no-padding">Group By #2</label>
                                    <select name="groupby_2" class='col-md-5 form-control selectpicker' style='margin-left:5px;' data-live-search='true'>
                                            <?php
											if (!empty($groupby_field)) {
												foreach ($groupby_field as $key => $value) {
													$selected = null;
													if (isset($session_info['groupby_2']) && !empty($session_info['groupby_2']) && $session_info['groupby_2'] == $key) {
														$selected = "selected";
													}
													?>
                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value ?></option>
                                            <?php }
										}
										?>
                                    </select>
                                </div>
                                 <div class="col-md-3 no-padding">
                                    <label class="search_label col-md-12 no-padding">Group By #3</label>
                                    <select name="groupby_3" class='col-md-5 form-control selectpicker' style='margin-left:5px;' data-live-search='true'>
                                            <?php
											if (!empty($groupby_field)) {
												foreach ($groupby_field as $key => $value) {
													$selected = null;
													if (isset($session_info['groupby_3']) && !empty($session_info['groupby_3']) && $session_info['groupby_3'] == $key) {
														$selected = "selected";
													}
													?>
                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value ?></option>
                                            <?php }
										}
										?>
                                    </select>
                                </div>
                            </fieldset>
                            <div class="col-md-12 margin-t-20 margin-b-20">
                                <button name="action" type="reset" id="id_reset" value="cancel" class="btn btn-line-sky pull-right margin-x-10" >Clear</button>
                                <button name="action" type="button" id="customersummary_search_btn" value="save" class="btn btn-line-parrot pull-right" >Search</button>
                                <div class="col-md-5 pull-right">
                                    <!--<div class="col-md-3"></div> -->
                                    <label class="search_label col-md-6" style="font-size:17px;text-align:right;">Display records in </label>
                                    <select name="search_in" class='col-md-5 form-control selectpicker' style='background: #ddd; width: 23% !important;' data-live-search='true'>
<?php
if (!empty($search_report)) {
	foreach ($search_report as $key => $value) {
		$selected = null;
		if (isset($session_info['search_in']) && isset($session_info['search_in']) && !empty($session_info['search_in']) && $session_info['search_in'] == $key) {
			//echo $key;exit;
			$selected = "selected";
		}
		?>
                                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value ?></option>
    <?php }
}
?>       
                                    </select>
                                </div>
                            </div>
                        </ul>
                    </form>
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
                        <table id="customersummary_grid" align="left" style="display:none;"></table>
                    </form>
                </div>  
            </div>
        </div>
    </div>
</section>




<? endblock() ?>	
<? end_extend() ?> 
