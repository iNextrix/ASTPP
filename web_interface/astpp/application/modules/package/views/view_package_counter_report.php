<?php extend('master.php') ?>
<?php startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
       build_grid("package_counter_grid","",<?php echo $grid_fields; ?>,<?php echo $grid_buttons; ?>);
	//build_grid("package_counter_grid","",<?php echo $grid_fields; ?>);
    });
</script>
<?php endblock() ?>

<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>

<?php startblock('content') ?>       
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
    </div> </div>
</section>

<?php

endblock();
end_extend();
