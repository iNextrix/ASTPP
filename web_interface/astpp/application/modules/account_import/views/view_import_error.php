<style>
.details_table td{
    text-shadow: 0 1px 0 white;
    /*font-weight: bold;*/
    padding: 6px;
    font-size: 11px;
    text-align: center;
/*    background-color: #E6E6E6;*/
    vertical-align:middle;
}
    </style>
<? extend('master.php') ?>
  <? startblock('extra_head') ?>
  <? endblock() ?>      
    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
	<? startblock('content') ?>
 <section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">  
					<fieldset >
						<legend>
							<span style="font-size:15px;padding:5px;font-family:Open sans,sans-serif;color:#163B80; ">Error In CSV File</span>
						</legend>
						<section class="slice color-three padding-b-20">
							<div class="w-section inverse no-padding">
								<div class="container">
									<div class="row">
										<div class="col-md-12">
											<?php echo $astpp_errormsg;?>
										</div>
									</div>
								</div>
							</div>
						</section>
					</fieldset>
				</div>
			</div>
		</div>
		<div class="col-md-12 padding-b-10">
			<div class="pull-right">
				<a href="<?= base_url().'accounts/customer_list/'?>"><input class="btn btn-line-parrot" id="customer_list" type="button" name="action" value="Back to Customer List" /> </a> 
			</div>
		</div>
	</div>
 </section>
        <? endblock() ?>
    <? startblock('sidebar') ?>
        Filter by
    <? endblock() ?>
<? end_extend() ?>  
    

