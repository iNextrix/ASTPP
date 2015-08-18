<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
       // build_grid("package_counter_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons;?>);
	build_grid("package_counter_grid","",<? echo $grid_fields; ?>);
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>       
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="package_counter_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div> </div><!--<br/><div class="pull-right padding-r-20">
      <a class="btn-tw btn " href="/package/package_counter_report/"><i class="fa fa-file-excel-o fa-lg"></i>Export CSV</a>
      
</div><br/><br/> -->
</section>

<? endblock() ?>	
<? end_extend() ?>  
