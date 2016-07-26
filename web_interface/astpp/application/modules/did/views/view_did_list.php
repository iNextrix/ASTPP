<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/validate.js"></script>
<script type="text/javascript" language="javascript">
 $(document).ready(function() {
    $( window ).load(function() {
	     $(".did_dropdown").removeClass("col-md-5");  
             $(".did_dropdown").addClass("col-md-3"); 
    });
    build_grid("did_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
    $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
    });
    $("#did_search_btn").click(function(){        
            post_request_for_search("did_grid","","did_search");
    });        
    $("#id_reset").click(function(){ 
      clear_search_request("did_grid","");
    });
    $('#purchase_did_form').validate({
      rules: {
        free_did_list: {
            required: true,
        }
      },
      messages:{
        free_did_list:{
	    required: "The Available DIDs field is required."
        }
      },
      errorPlacement: function(error, element) {
        error.appendTo('#err');
      }
    });
    $("#purchase_did").click(function () {
      $("#search_generate_bar").slideToggle("slow");
    });
 });
</script>
<style>
 #err{
   height:20px !important;width:100% !important;float:left;
 }
 label.error {
  float: left; color: red;
  padding-left: .3em; vertical-align: top;  
  padding-left:0px;
  margin-top:-10px;
  width:100% !important;
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
<?php 
if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
?>
<input type="button" class="btn btn-line-parrot margin-l-20 margin-b-10" name="purchase_did" value="Purchase DID" id="purchase_did" style="margin-left:1200px; margin-top:10px;">    
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
	  <div class="row">
            <div class="col-md-12 no-padding margin-b-10" >
                <div class="slice color-three pull-left col-md-12 padding-t-20" id="search_generate_bar" style="display:none;cursor: pointer;">
		  <div class='margin-b-20'>
		    <form id="purchase_did_form" name='purchase_did_form' method="post" action="<?= base_url() ?>did/did_reseller_purchase/" enctype="multipart/form-data">
			<div class="col-md-4">
			    <label class="col-md-4 no-padding">Available DIDs : </label>
				<div class="col-md-8 no-padding sel_drop">
				    <? echo $didlist; ?>
				    <span id="err"></span>
				</div>
			</div>
			<input class="margin-l-20 btn btn-success" name="action" value="Purchase DID" type="submit">
		    </form>
		    </div>
                </div>
            </div>
            </div>
      </div>  
    </div>
</section>
<?php }?>
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
<section class="slice color-three padding-b-20">
  <div class="w-section inverse no-padding">
    <div class="container">
      <div class="row">
        <div class="col-md-12">      
	  <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
	     <table id="did_grid" align="left" style="display:none;"></table>
	  </form>
        </div>  
      </div>
    </div><br/>
  </div>
</section>
<? endblock() ?>
<? end_extend() ?>