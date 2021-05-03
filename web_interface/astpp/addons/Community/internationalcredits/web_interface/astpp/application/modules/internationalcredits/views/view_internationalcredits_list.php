<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("internationalcredits_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked);
        });
        $("#internationalcredits_search").click(function(){
            post_request_for_search("internationalcredits_grid","","internationalcredits_list_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("internationalcredits_grid","internationalcredits_clearsearchfilter");
            $('#internationalcredits_grid').flexReload();
        });
        
    });
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>
<section class="slice color-three">
    <div class="w-section inverse p-0">
        <div class="col-12">
                <div class="portlet-content mb-4"  id="search_bar" style="cursor:pointer; display:none">
                        <?php echo $form_search; ?>
                </div>
            </div>
    </div>
</section>
<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
    	         <div class="card col-md-12 pb-4">      
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="internationalcredits_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
    </div>
</section>
<? endblock() ?>	
<? end_extend() ?>