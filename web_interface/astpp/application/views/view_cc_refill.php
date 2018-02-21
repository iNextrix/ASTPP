<script type="text/javascript">
  $(document).ready(function() {
  $("#frm_refill").validate({
	  rules: 
	  {
		  pennies: "required",
	  }
  });

  });

</script>

<div class="content-box content-box-header ui-corner-all float-left full">

    <div class="ui-state-default ui-corner-top ui-box-header">

        <span class="ui-icon float-left ui-icon-signal"></span>

        Refill Calling Card

    </div>

    <div class="content-box-wrapper">

    <form action="<?=base_url()?>callingcards/refill" id="frm_refill" method="POST" enctype="multipart/form-data">

    <ul style="width:600px">    

    <input type="hidden" name="cardnumber" value="<?php echo @$cc['cardnumber'];?>"  size="20" />

    <li>

    <label class="desc">Card Number</label>
    <?php echo @$cc['cardnumber'];?>
    </li>
    <li>
    <label class="desc">Amount</label>
    <input class="text field medium" type="text" name="pennies"  size="5" value="0.00"/>
    </li>
    </ul>
    <div style="margin-top:20px; height:50px; width:100%; float:left">
    <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Refill" />
    </div>
    </form>
    </div>

</div>