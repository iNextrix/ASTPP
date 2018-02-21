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
 /* $("#context").change(function(){
    $('#context_name').text('');
    return false;
  });*/
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
//alert(numbers);
      if(document.getElementById('ANI_ID').value == "")
      {
//           
	  $('#ani_name').text( "Please Enter Ani Number." );
	  document.getElementById('ANI_ID').focus();
	  return false;
      }
      if(numbers == true)
      {
           
	  $('#ani_numeric').text( "Ani field must contain a numeric value." );
	  document.getElementById('ANI').focus();
	  return false;
      }
      
   /*  if(document.getElementById('context').value == "")
      {
//           
	  $('#context_name').text( "Please Enter Context!" );
	  document.getElementById('context').focus();
	  return false;
      }
*/

      $('#myform1').submit();
       
} 
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        

<section class="slice color-three padding-b-20">
		<div class="row">
                <div class="col-md-12" >

           <form method="POST" action="<?= base_url() ?>user/user_animap_action/add/" enctype="multipart/form-data" name="ani_form" id="myform1" >
                <div style="padding-top:25px;"><label class="col-md-1" style="padding-left:70px;">ANI:</label><input class="col-md-2 form-control" name="ANI" id="ANI_ID" size="20" type="text">
<!--                <label class="col-md-1" style="padding-left:45px;">Context:</label><input class="col-md-2 form-control" name="context" id="context" size="20" type="text">-->
		    <input class="margin-l-20 btn btn-success" name="action" value="Map ANI" type="button" onclick="validateForm();"></div>
            </form>  
			
          </div>  
<div style="padding-left:150px;">
	
</div> 
	<span style="color:red;margin-left:120px;float:left;" id="ani_numeric"></span>   
	<span style="color:red;margin-left:120px;float:left;" id="ani_name"></span> 
        
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
