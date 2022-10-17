<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        var sip_device_id ="<?php echo isset($sip_device_id) ? $sip_device_id : ''; ?>";
        $('#sip_device_id').val(sip_device_id);
        build_grid("voice_broadcast_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked'));
        });
        $("#voice_broadcast_search_btn").click(function() {
            post_request_for_search("voice_broadcast_grid","","voice_broadcast_list_search");
        });
        $("#id_reset").click(function() {
            clear_search_request("voice_broadcast_grid","");
        });
        $(".reseller_id").change(function(){
            if(this.value!=""){
				$.ajax({
					type:'POST',
					url: "<?= base_url()?>voice_broadcast/customer_depend_list/",
					data:"reseller_id="+this.value,
					success: function(response) {
						$("#accountid").html(response);
						$('.accountid').selectpicker('refresh');
					}
				});
			}	
        }); 
        $(".reseller_id").change();
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
                            <table id="voice_broadcast_grid" align="left" style="display:none;"></table>
                        </form>
                </div>
    </div>
</section>
<? endblock() ?>
<? end_extend() ?>
