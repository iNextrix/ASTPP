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
        <?=$page_title?>
    <? endblock() ?>
	<? startblock('content') ?>
  <section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">         
	    <fieldset >
                
		<legend><span style="font-size:15px;padding:5px;font-family:Open sans,sans-serif;color:#163B80; ">Error In CSV File</span></legend>
                   
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
          
		    Records Imported Successfully: <?= $impoted_count?><br/>
                    Records Not Imported : <?= $failure_count?>
		</div>  
            </div>
        </div>
    </div>
</section>
  </div>
        </div>
    </div> 
<br/>

                    <?php if (isset($packageid) && $packageid != "") { ?>
<div class="col-md-12 padding-b-10">
                   <div class="pull-right">
                        <a href="<?= base_url().'package/package_pattern_list/'.$packageid?>"><input class="btn btn-line-sky margin-x-10" id="back_to_list" type="button" name="action" value="Back to Package Patterns List" /> </a>
                        <a href="<?= base_url().'package/package_patterns_error_download/'?>"><input class="btn btn-line-parrot" id="download_file" type="button" name="action" value="Download Errors" /> </a>
           </div></div>          <?}?>
            </fieldset>
</section>		
                   
        <? endblock() ?>
    <? startblock('sidebar') ?>
        Filter by
    <? endblock() ?>
<? end_extend() ?>  
    

