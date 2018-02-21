<script type="text/javascript" src="/js/validate.js"></script>

	<script type="text/javascript">

		$(document).ready(function() {

		// validate signup form on keyup and submit

		$("#frm_manage_ani").validate({

			rules: {
				ANI: "required",
				limittime: "required"
			}

		});

		});

	</script>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        

        <div class="portlet-header ui-widget-header"><?=isset($did)?"Edit":"Add New"?> Map ANI to Account<span class="ui-icon ui-icon-circle-arrow-s"></span></div>

        <div class="portlet-content">

        <form action="<?=base_url()?><?=isset($did)?"useranimapping/animappinglists/edit":"useranimapping/animappinglists/add"?>" id="frm_manage_ani" method="POST" enctype="multipart/form-data">

       
        <ul style="width:600px">        
        <li>
        <label class="desc">Map ANI to Account</label>		
        <input type="text" name="ANI" id="ANI" class="text field medium"  size="20" value="" />		
        </li>        
        </ul>
        <div style="width:100%;float:left;height:50px;margin-top:20px;">
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="<?=isset($did)?"Save...":"Map ANI";?>" /> 

        </div>

        </form>

        </div>

</div>