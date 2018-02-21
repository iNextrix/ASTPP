<script type="text/javascript">

		$(document).ready(function() {

		// validate signup form on keyup and submit

		$("#frm_status_update").validate({

			rules: {
				starting: "required",
				ending: "required",
			}

		});

		});

	</script>

<div class="content-box content-box-header ui-corner-all float-left full">

                <div class="ui-state-default ui-corner-top ui-box-header">

                    <span class="ui-icon float-left ui-icon-signal"></span>

                    Change Calling Card Status

                </div>

                <div class="content-box-wrapper">

                <form action="<?=base_url()?>callingcards/update_status" id="frm_status_update" method="POST" enctype="multipart/form-data">

                

                <ul style="width:600px">

                  <li>
                  <label class="desc">Starting Sequence Number:</label>
				  <input type="text" name="starting" value=""  />
                  </li>
                  <li>
                  <label class="desc">Ending Sequence Number:</label>
				  <input type="text" name="ending" value=""  />
                  </li>                  
                  <li>
                  <label class="desc">Set Status:</label>

                  <select class="select field medium" name="status" >

                  <option value="1">ACTIVE</option>

                  <option value="0">INACTIVE</option>

                  <option value="2">DELETED</option>                

                  </select>

                  </li>

                </ul>

                <div style="margin-top:20px; height:50px; width:100%; float:left">

                <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Update Status on Card(s)" />

                </div>

                </form>

                </div>

</div>