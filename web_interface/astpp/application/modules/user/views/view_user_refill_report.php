<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("refillreport_grid","",<? echo $grid_fields ?>,"");
        $("#user_refill_report_search_btn").click(function(){
            post_request_for_search("refillreport_grid","","user_refill_report_search");
        });
        $("#id_reset").click(function(){
            clear_search_request("refillreport_grid","");
        });
    });
</script>
<style>
section.slice	 {
    position: absolute !important;
    margin-left: 20% !important;
    color: #5E5E5E !important;
    display: inline-block !important;
    width: 78% !important;
}
.flexigrid div.hDivBox{
    float: left !important;
    padding-right: 40px !important;
    width: 100% !important;
    overflow: scroll !important;
}
</style>

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
                    <?php echo $form_search; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="slice color-three padding">
    <div class="w-section inverse no-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-12 color-three padding-b-20">
                    <table id="refillreport_grid" align="left" style="display:none;"></table>
                </div>  
            </div>
        </div>
    </div>
</section>

<? endblock() ?>	
<? end_extend() ?>  
 
