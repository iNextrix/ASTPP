
<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("did_form");
    })
</script>
<section class="slice gray no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 padding-l-16 margin-b-10">
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
            <div style="color:red;margin-left: 60px;">
                <?php if (isset($validation_errors)) echo $validation_errors; ?> 
            </div>

 <form action="<?= base_url() ?>user/user_dids_action/edit/" id="frm_callshop" method="POST" enctype="multipart/form-data">
            <ul class="padding-15">
              <fieldset>
    <legend>Edit</legend>
                <input type="hidden" readonly name="didid" id="didid" value="<?= @$didinfo['id']; ?>">
                <li class="col-md-12">   
                    <label class="col-md-3 no-padding">Number:</label>     
                    <input class="col-md-5 form-control" readonly type="text" value="<?= @$didinfo['number'] ?>">
                </li>
                <li class="col-md-12">   
                    <label class="col-md-3 no-padding">Country:</label>     
                    <input class="col-md-5 form-control" readonly type="text" value="<?= @$didinfo['country'] ?>">
                </li>
                <li class="col-md-12">   
                    <label class="col-md-3 no-padding">Province</label>     
                    <input class="col-md-5 form-control" readonly type="text" value="<?= @$didinfo['province'] ?>">
                </li>
                <li class="col-md-12">   
                    <label class="col-md-3 no-padding">City</label>
<input class="col-md-5 form-control" type="text" readonly value="<?= @$didinfo['city'] ?>">
                </li>
                <!--<li style="line-height:30px;">   
                   <span style="width:130px;">Increment:</span>
                    <label><?= @$didinfo['inc'] ?> </label>
                </li><li style="line-height:30px;">   
                   <span style="width:130px;">Cost:</span>
                    <label><?= @$didinfo['cost'] ?> </label>
                </li>                
                <li style="line-height:30px;">   
                    <span style="width:130px;">Included Second:</span>
                    <label><?= @$didinfo['includedseconds'] ?>  </label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Setup Fee:</span>
                    <label><?= @$didinfo['setup'] ?> </label>
                </li>
                <li style="line-height:30px;">      
                    <span style="width:130px;">Monthly fee:</span>
                    <label>  <?= @$didinfo['monthlycost'] ?>  </label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Connection Fee:</span>
                    <label><?= @$didinfo['connectcost'] ?> </label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Disconnection Fee:</span>
                    <label><?= @$didinfo['disconnectionfee'] ?> </label>
                </li>-->
		 <li class="col-md-12">   
                    <label class="col-md-3 no-padding">Call Type</label>     
                    <label class=""  style=""> <div style="width:500px;"><? echo $call_type; ?></div></label>
                </li>
               
                <li class="col-md-12">   
                    <label class="col-md-3 no-padding">Dialstring</label>
                     <input size="20" class="col-md-5 form-control" name="extension" value="<?= @$didinfo['extensions'] ?>">
                </li>
            </ul>
              </fieldset>
            <input type="submit" class="btn btn-line-parrot pull-center " style="margin-left:300px; margin-top:10px;"  name="action" value="Save" />
            <br>
            <br><hr>
            <TMPL_VAR NAME= "status">
            
        </form>
            </div>  
       
    </section>
  </div>
</div>

