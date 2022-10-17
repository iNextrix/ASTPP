<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
/**********
ASTPP  3.0
Batch Delete
**********/
function search_btn(){
    document.getElementById('custom_rate_batch_dlt').style.display = 'block';
}

function check_btn(){
	 	$.ajax({
			type: "POST",
			url: "<?= base_url()?>/rates/custom_rates_list_delete/",
			data:'',
			success:function(alt) {
			 if(alt > 0){
		  	   post_request_for_batch_delete("custom_rate_grid",alt,"custom_rate_list_search");
	  	           $('#custom_rate_grid').flexOptions({newp:1}).flexReload();
		 	//   location.reload();
			  }else{
			     alert('No record found');
			  }
			}
		});
}
/************************/
    $(document).ready(function() {
        build_grid("custom_rate_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked')); //if you want to select/deselect checkboxes use this
        });
        $("#custom_rate_list_search_btn").click(function(){
            post_request_for_search("custom_rate_grid","","custom_rate_list_search");
        });        
        $("#id_reset").click(function(){
/*****
ASTPP  3.0 
Batch Delete 
*****/
    document.getElementById('custom_rate_batch_dlt').style.display = 'none';
/*******************/
            clear_search_request("custom_rate_grid","");
        });

         $("#batch_update").click(function(){
            submit_form("custom_rate_batch_update");
        })
        $("#id_batch_reset").click(function(){
            $(".update_drp").each(function() {
				var name=this.name;
				var split_name;
				if(name!=undefined){
					split_name=name.split("[");
					$('#'+split_name[0]).hide();
					$('#'+split_name[0]).val("");
					$('.update_drp').selectpicker('refresh');
				}else{
					$('.update_drp').val("1");
					$('.update_drp').selectpicker('refresh');
				}
            });
            $(".trunk_id").val("");
            $('.trunk_id').selectpicker('refresh');
            
            
        });
        
       $(".update_drp").change(function(){
           var inputid = this.name.split("[");
           if(this.value != "1"){
               $('#'+inputid[0]).show();
           }else{
               $('#'+inputid[0]).hide();
           }
       }).each(function(){
            var inputid = this.name.split("[");
            if(this.value != "1"){
                $('#'+inputid[0]).show();
            }else{
                $('#'+inputid[0]).hide();
            }
        });

    });
</script>

<script>
	$(document).ready(function() {
		$(".reseller_id_search_drp").change(function(){
		 	if(this.value!=""){
				var reseller_id=$("#reseller1").val();
				$.ajax({
				    type:'POST',
				    url: "<?= base_url()?>/custom_rates/customer_customerlist_customrates_search/",
				    data:"reseller_id="+reseller_id,  
				    success: function(response) {
				    	$("#accountid2").html(response);
							$('.selectpicker').selectpicker('refresh');
				  	}
				}); 
			}else{
					$("#accountid2").html("");					
			}
  	});
  	
  	
  	$(".reseller_id_search_drp").change(); 
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
                <div class="portlet-content mb-4"  id="search_bar" style="display:none;">
                        <?php echo $form_search; ?>
                </div>
            </div>
    </div>
</section>
<section class="slice color-three">
    <div class="w-section inverse p-0">
        <div class="col-12">
                <div class="portlet-content mb-4"  id="update_bar" style="display:none;">
                        <?php echo $form_batch_update; ?>
                </div>
            </div>
    </div>
</section>
<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
                <div class="card col-md-12 pb-4">      
                        <form method="POST" enctype="multipart/form-data" id="ListForm">
                            <table id="custom_rate_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
    </div>
</section>

<? endblock() ?>	
<? end_extend() ?>  
