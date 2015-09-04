
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.7.1.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/facebox.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/flexigrid.js"></script>
<script type="text/javascript" src="/js/validate.js"></script>

<script type="text/javascript">
    $("#submit").click(function(){
		submit_form("frm_manage_did");
    })
</script>
<script type="text/javascript">

    $(document).ready(function() {

        // validate signup form on keyup and submit

        $("#frm_manage_did").validate({

            rules: {
                number: "required",
                limittime: "required"
            }

        });

    });

</script>


<section class="slice gray no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 margin-b-10">
	        <div class="col-md-10"><b><? echo $page_title; ?></b></div>
	  </div>
     </div>
    </div>
  </div>    
</section>


<div>
  <div>
    <section class="slice color-three no-margin">
        <div class="w-section inverse no-padding">
          
   

        <form action="<?= base_url() ?><?= isset($did) ? "did/did_reseller_edit/edit" : "did/did_reseller_edit/add" ?>" id="frm_manage_did" method="POST"  enctype="multipart/form-data">
            	<fieldset>
            <legend>Edit</legend>
            <ul class="padding-15">        
                <li class="col-md-8">
                    <label class="col-md-3 no-padding">DID :</label>
                        <input type="text" class="col-md-5 form-control" readonly name="note" value="<?= @$did ?>" />
                    
                </li>      
               <!-- <li class="col-md-8">
                    <label class="col-md-3 no-padding">Country :</label>
                    <input type="text" class="col-md-5 form-control" name="number" value="<?//= @$reseller_didinfo['country'] ?>" />
                </li>        

                 <li class="col-md-8">
                    <label class="col-md-3 no-padding">Province :</label>     
                    <input type="text" class="col-md-5 form-control" name="number" value="<?//= @$reseller_didinfo['province'] ?>" />
                </li>

                <li class="col-md-8">
                    <label class="col-md-3 no-padding">City :</label>
                    <input type="text" class="col-md-5 form-control" name="number" value="<?//= @$reseller_didinfo['city'] ?>" />
                </li>  -->     	
                 <!--<li class="col-md-8">
                    <label class="col-md-3 no-padding">Provider :</label>
                    <input type="text" class="col-md-5 form-control" name="number" value="<?//= @$reseller_didinfo['provider_id'] ?>" />
                </li>-->

              <!--  <li class="col-md-8">
                    <label class="col-md-3 no-padding">Account :</label>        
                    <label class="col-md-5 form-control">
                    if (//$reseller_didinfo['accountid'] == '0') {
                        //echo "";
                    //}
                   </label>        
                </li> -->       
                <?//echo "<pre>";print_r($reseller_didinfo);echo "</pre>";?>
        	      <li class="col-md-8">
                        <label class="col-md-3 no-padding">Call Type</label>
                  		  <select name="call_type" class="col-md-5 form-control">
                  		    <option value="0" <?if($reseller_didinfo['call_type'] == 0){ echo 'selected="selected"'; }?>>PSTN</option>
                  		    <option value="1" <?if($reseller_didinfo['call_type'] == 1){ echo 'selected="selected"';}?>>Local</option>
                  		    <option value="2" <?if($reseller_didinfo['call_type'] == 2){ echo 'selected="selected"'; }?>>Other</option>
                		    </select>
        		    </li>
                 <li class="col-md-8">
                    <label class="col-md-3 no-padding">Destinations :</label>
                    <input type="text" class="col-md-5 form-control" name="extensions" value="<?= @$reseller_didinfo['extensions'] ?>" />
                </li>
                 <li class="col-md-8">
                    <label class="col-md-3 no-padding">Setup Fee :</label>
                    <input type="text" class="col-md-5 form-control" name="setup" value="<?= @$reseller_didinfo['setup'] ?>" />
                </li>
  <!--                <li class="col-md-12">
		      <label class="col-md-3 no-padding">Disconnection Fee:</label>
		      <label class="value_bold">&nbsp;<?= @$reseller_didinfo['disconnectionfee'] ?></label>
		      <input type="text" class="text field" name="disconnectionfee"  size="20"  value="<?= @$reseller_didinfo['disconnectionfee'] ?>" />
		  </li>   -->  
                 <li class="col-md-8">
                    <label class="col-md-3 no-padding">Monthly Fee :</label>
                    <input type="text" class="col-md-5 form-control" name="monthlycost" value="<?= @$reseller_didinfo['monthlycost'] ?>" />
                </li>        
                 <li class="col-md-8">
                    <label class="col-md-3 no-padding">Connection Fee :</label>
                    <input type="text" class="col-md-5 form-control" name="connectcost" value="<?= @$reseller_didinfo['connectcost'] ?>"/>
                </li>        
                 <li class="col-md-8">
                    <label class="col-md-3 no-padding">Included Seconds :</label>
                    <input type="text" class="col-md-5 form-control" name="includedseconds" value="<?= @$reseller_didinfo['includedseconds'] ?>"/>
                </li>        
                 <li class="col-md-8">
                    <label class="col-md-3 no-padding">Cost :</label>
                    <input type="text" class="col-md-5 form-control" name="cost" value="<?= @$reseller_didinfo['cost'] ?>"/>
                </li>        
                 <li class="col-md-8">
                    <label class="col-md-3 no-padding">Increments :</label>
                    <input type="text" class="col-md-5 form-control" name="inc" value="<?= @$reseller_didinfo['inc'] ?>"/>
                </li>                


               <!--  <li class="col-md-8">
                    <label class="col-md-3 no-padding">Prorate :</label>
                    
                    <select name="prorate" class="col-md-5 form-control" style="width:170px;" >
                        <option value="1" <?php if (@$reseller_didinfo['prorate'] == "1") {
                        echo "selected='selected'";
                    } ?> >YES</option>
                        <option value="0" <?php if (@$reseller_didinfo['prorate'] == "0") {
                        echo "selected='selected'";
                    } ?> >NO</option>
                    </select>
                </li>-->

                 <!--<li class="col-md-8">
                    <label class="col-md-3 no-padding">Dial As :</label>
                    <input type="text" class="col-md-5 form-control" name="number" value="<?= @$did['reseller_didinfo'] ?>"/>
                </li> -->                       
            </ul>        
            </fieldset>
            <center>
           <div style="width:100%;float:left;height:50px;margin-top:20px;">
	            <input type="button" class="btn btn-line-parrot" id='submit' style="margin-left:5px;" name="action" value="<?= isset($did) ? "Save" : "Insert"; ?>" /> 
             <input type="button" onclick="location.href = '<?= base_url() ?>did/did_list/';" class="btn btn-line-sky margin-x-10" name="action" value="Cancel" /> 
            </div></center>
        </form>
       </div>      
    </section>        
 </div>
</div>



