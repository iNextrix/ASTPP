<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Edit Did<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
        <form action="<?= base_url() ?>user/editdid" id="frm_callshop" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="did" value="<?= @$number ?>">

            <?//echo "<pre>";print_r(@$didinfo); ?>


            <ul style="width:600px; list-style:none;">
                <li>   
                    <label class="desc">Number:</label>     
                    <label class="desc"><?= @$didinfo['number'] ?></label> 
                </li>
                <li>   
                    <label class="desc">Connection Fee:</label>
          
                    <label class="desc"><?= @$didinfo['connectcost'] ?> </label>
                </li>
                <li>   
                    <label class="desc">Included Second:
                    </label>
              <label class="desc">       <?= @$didinfo['includedseconds'] ?>  </label>
                </li>
                <li>   
                    <label class="desc">Cost:
                    </label>
                       <label class="desc"> <?= @$didinfo['cost'] ?>   </label>
                </li>
                <li>   
                    <label class="desc">Monthly fee:
                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   <label class="desc">  <?= @$didinfo['monthlycost'] ?>  </label>
                </li>


                <li>
                    <label class="desc">Country:
                    </label>
                   <label class="desc">  <?=@$didinfo['country'] ?> </label>

                </li>
                <li>
                    <label class="desc">Province
                    </label>
                <label class="desc">     <?=@$didinfo['province'] ?> </label>

                </li>
                <li>
                    <label class="desc">City
                     </label>
                    <label class="desc">      <?= @$didinfo['city'] ?></label>


                </li>
                <li>
                    <label class="desc">
                        <!--              <acronym title="">-->
                        Dialstring
                        <!--              </acronym>-->

                    </label>
                    <input size="20" class="text field medium" name="extension" value="<?= @$didinfo['extensions'] ?>">

                </li>

            </ul>
            <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Save..." />
            <br>

            <br><hr>
            <TMPL_VAR NAME= "status">
        </form>
    </div>
</div>
