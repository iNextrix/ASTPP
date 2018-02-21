<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Periodic Charges<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
            <form method="post" action="<?=base_url()?><?=isset($periodicCharge)?"rates/periodiccharges/edit":"rates/periodiccharges/add"?>" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?=@$periodicCharge['id']?>"  />
            <ul style="width:600px">
            <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Periodic Charges Information</span></legend>
            <li>
            <label class="desc">Description:</label><input class="text field medium" type="text" name="desc" value="<?=@$periodicCharge['description']?>"  size="20" />
            </li>
            <li>
            <label class="desc">Pricelist:</label>
            <select name="pricelist" class="select field medium" >
            <option value=""></option>
			<?=$pricelists?>
            </select>
            </select>
            </li>
            <li>
            <label class="desc">Rate:</label>
            <input class="text field medium" type="text" name="charge" value="<?=$this->common_model->calculate_currency(@$periodicCharge['charge'],'','',true,false)?>"  size="8" />
            </li>
            <li>
            <label class="desc">Cycle:</label>
            <?=form_select_default('sweep',$sweeplist,@$periodicCharge['sweep'],array("class"=>"select field medium"))?>
            </li>
            </fieldset>
			</ul>
            <div style="width:100%;float:left;height:50px;margin-top:20px;">
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="<?=isset($periodicCharge)?"Save...":"Insert..."?>" />
            </div>
            </form>            
            </div>
</div>