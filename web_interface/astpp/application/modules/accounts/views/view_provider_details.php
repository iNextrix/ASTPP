<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        
        build_grid("invoices_grid","<?php echo base_url(); ?>accounts/provider_details_json/invoices/<?= $account_data[0]['id']; ?>",<? echo $invoice_grid_fields ?>,"");

       // $("#tab7").click(function(){
        build_grid("cdrs_grid","<?php echo base_url(); ?>accounts/customer_details_json/reports/<?= $account_data[0]['id']; ?>",<? echo $cdrs_grid_fields ?>,"");        
	//}); 
        

        build_grid("ipmap_grid","<?php echo base_url(); ?>accounts/customer_ipmap_json/<?= $account_data[0]['id']; ?>/provider/",<? echo $ipmap_grid_field ?>,"");

        $(".sweep_id").change(function(e){
            if(this.value != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url()?>/accounts/customer_invoice_option",
                    data:"sweepid="+this.value, 
                    success: function(response) {
                        $(".invoice_day").html(response);
                        $('.invoice_day').show();
                        $('label[for="Billing Day"]').show()
                    }
                });
            }else{
                $('label[for="Billing Day"]').hide()
                $('.invoice_day').css('display','none');                
            }
        })

    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        

  <ul class="tabs" data-persist="true">
        <li><a href="#provider_details">Provider Details</a></li>
        <li><a href="#accounts">IP Settings</a></li>        
        <li><a href="#invoices">Invoices</a></li>
         <li><a id ='tab4' href="#cdrs">CDRs</a></li>
    </ul>	
<div class="tabcontents">        
      <div>
          <div class="col-md-12">
            <section class="slice color-three no-margin">
                <div class="w-section inverse no-padding">
            <div id="provider_details">
                    <div style="color:red;margin-left: 60px;">
                        <?php
                        if (isset($validation_errors)) {
                           $validation_array=json_decode($validation_errors);
                           if(is_object($validation_array)){
                           $validation_array = get_object_vars($validation_array);
                           foreach($validation_array as $key=>$value)
		              echo $value."<br/>";
                           }
                           else
		              echo $validation_errors;
                           
                        }
                        ?>
                    </div>
                      <?php echo $form; ?>
                </div>      
           
  
    
    <div id='accounts'>
        <!-- <div class="container">
          <div class="row">
            <section class="slice color-three no-margin">
                <div class="w-section inverse no-padding col-md-12">  -->
       <!--         <div class="col-md-12 no-padding">
                        <span class="ui-icon float-left ui-icon-signal"></span>IP MAP
                        <table id="ipmap_grid" align="left" style="display:none;"></table> 
                </div>  -->
                <div class="col-md-12 color-three padding-b-20">
                    <form method="post" name="ip_map" id="ip_map" action="<?= base_url() ?>accounts/customer_ipmap_action/add/<?= $account_data[0] ['id'] ?>/provider/" enctype="multipart/form-data"><br/>
                        <label class="col-md-1">Name: </label><input class="col-md-2 form-control" name="name" size="16" type="text">
                        <label class="col-md-1">IP: </label><input class="col-md-2 form-control" name="ip" size="22" type="text">
			<label class="col-md-1">Prefix: </label><input class="col-md-2 form-control" name="prefix" size="16" type="text">
                        
                        <input class="margin-l-20 btn btn-success" name="action" value="Map IP" type="submit">
                    </form>
                </div>

                <div class="col-md-12 color-three padding-b-20">
                        <table id="ipmap_grid" align="left" style="display:none;"></table> 
                </div>
       <!--         </div>
            </section>
         </div>
        </div>
    </div>    -->
   </div>
<div id='invoices'>
<!--      <div class="container">
          <div class="row">
            <section class="slice no-margin">
                <div class="w-section inverse no-padding col-md-12"> -->
                      <div class="col-md-12 color-three padding-b-20">
                          <table id="invoices_grid" align="left" style="display:none;"></table>                 
                      </div>
<!--                </div>
             </section>
          </div>
      </div>-->
    </div>
    
    <div id='cdrs'>
<!--      <div class="container">
          <div class="row">
            <section class="slice no-margin">
                <div class="w-section inverse no-padding"> -->
                    <div class="col-md-12 color-three padding-b-20">
                       <table id="cdrs_grid" align="left" style="display:none;"></table>              
                    </div>
<!--                </div>
             </section>
          </div>
      </div> -->
    </div>
   
<!--
    <!--Account Tab End-->
  <!--  <div id='invoices'>
      <div class="container">
          <div class="row"> 
            <section class="slice color-three no-margin">
                <div class="w-section inverse no-padding"><span class="ui-icon float-left ui-icon-signal"></span>Invoice List
                    <table id="invoices_grid" align="left" style="display:none;"></table>                 
                </div>
             </section>
          </div>
      </div>
    </div>

<div id='cdrs'>
<!--      <div class="container">
          <div class="row">
            <section class="slice no-margin">
                <div class="w-section inverse no-padding"> -->
             <!--       <div class="col-md-12 color-three padding-b-20">
                       <table id="cdrs_grid" align="left" style="display:none;"></table>              
                    </div>
<!--                </div>
             </section>
          </div>
      </div> -->
  <!--  </div> -->
 
</div>     
    </section>        
  </div>
</div>    
</div>

<? endblock() ?>	
<? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>  
