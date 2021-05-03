<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script>
$(document).ready(function(){
  $("#ANI_ID").change(function(){
    $('#ani_name').text('');
    return false;
  });
  $("#ANI_ID").change(function(){
    $('#ani_numeric').text('');
    return false;
  });
});

</script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("animap_grid","",<? echo $grid_fields; ?>,"");
    });
function validateForm(){
      var val=document.getElementById('ANI_ID').value;
      var length1=val.length;
      var numbers  = isNaN(val);
      if(document.getElementById('ANI_ID').value == "")
      {      
	  $('#ani_name').text( "Please Enter Caller Id Number." );
	  document.getElementById('ANI_ID').focus();
	  return false;
      }     
      if(length1 > 20)
      {    
	  $('#ani_name').text( "Caller Id must be less then 20 characters." );
	  document.getElementById('ANI').focus();
	  return false;
      }
      $('#myform1').submit();
       
} 
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        

<section class="slice color-three padding-b-20">
		<div class="row">
                <div class="col-md-12" >

           <form method="POST" action="<?= base_url() ?>user/user_animap_action/add/" enctype="multipart/form-data" name="ani_form" id="myform1" >
                <div style="padding-top:25px;"><label class="col-md-1 margin-l-20" ><?php echo gettext('Caller ID:') ?></label><input class="col-md-2 form-control" name="ANI" id="ANI_ID" size="20" type="text" maxlength="20">
		    <input class="margin-l-20 btn btn-success" name="action" value="Caller ID" type="button" onclick="validateForm();"></div>
            </form>  
			
          </div>  
<div style="padding-left:150px;">
	
</div> 
	<span style="color:red;margin-left:130px;float:left;" id="ani_numeric"></span>   
	<span style="color:red;margin-left:140px;float:left;" id="ani_name"></span> 
        
</section>

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">   
		  <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="animap_grid" align="left" style="display:none;"></table>
        </form>
                   </div>  
            </div>
        </div><br/>
    </div>
</section>


<? endblock() ?>	

<? end_extend() ?>  
