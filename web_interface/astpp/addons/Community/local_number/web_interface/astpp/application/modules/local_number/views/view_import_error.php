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
 <section class="slice color-three bp-4">
	<div class="w-section inverse p-0">
    	<div class="row">
        	<div class="col-md-12">
                <div class="card">
					<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext("Error In CSV File"); ?></h3>  
							<div class="col-md-12 p-4">
														Records Imported Successfully: <?= $import_record_count; ?><br/>
														Records Not Imported : <?= $failure_count?></div>  		
							</div>		
				</div>
		</div>	 
             <div class="col-md-12 pb-2 mt-4 pr-0">
                   <div class="float-right">
                        <a href="<?= base_url().'local_number/local_number_error_download/'?>"><input class="btn btn-success" id="dwnld_err" type="button" name="action" value="Download Errors" /> </a>
						<a href="<?= base_url().'local_number/local_number_list/'?>"><input class="btn btn-secondary" id="local_number_list" type="button" name="action" value="Back to Local Number List" /> </a>  </div></div>
					</div>
			 </div>
		</div>
	</div>
</section>	
        <? endblock() ?>
    <? startblock('sidebar') ?>
        Filter by
    <? endblock() ?>
<? end_extend() ?>  
    

