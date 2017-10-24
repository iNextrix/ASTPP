<? extend('master.php') ?>
<? startblock('extra_head') ?>

<!--
ASTPP  3.0 
For Email Template Changes
-->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/tinymce/tinymce.min.js">

</script>
<script type="text/javascript">

tinymce.init({
  mode : "specific_textareas",
  editor_selector: 'Emailtemplate',
  height: 300,
  width: 700,
  theme: 'modern',
  plugins: [
    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'emoticons template paste textcolor colorpicker textpattern imagetools'
  ],
  toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  toolbar2: 'print preview media | forecolor backcolor emoticons',
  image_advtab: true,
  templates: [
    { title: 'Test template 1', content: 'Test 1' },
    { title: 'Test template 2', content: 'Test 2' }
  ],
  content_css: [
    '<?php echo base_url(); ?>assets/css/tinymce_fast_font.css',
    '<?php echo base_url(); ?>assets/css/tinymce_codepen_min.css'
  ],
  
   setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    }
  
 });
</script>

<!--***************************************************************-->


	<script type="text/javascript" src="<?php echo base_url(); ?>assets/markup/markitup/sets/default/set.js"></script>
<script type="text/javascript">
$(function() {
	$('#template').markItUp(mySettings);

});
</script>

<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery('#myform').submit(function(){
    
   jQuery("input[type=file]").bind(function(){
     jQuery(this).rules("add", {
       size: '10000',
       messages: {
         accept :"Only jpeg, jpg or png images"
       }
     });
   });
});
  });

    
function addFile(){
var file_name = document.getElementById("file").value;
var file_size=0;
$('.fileupload').each(function() { 
	file_size=file_size+(this.files[0].size/1048576);
	});
if(file_size > 20)
{
	alert("Attachment size can not more than 20 MB.");
}
else{
if(file_name == ''){
	alert("Please select Attach file");

}
else{
  document.getElementById('welcomeDiv').style.display = "none";
  var cnt = $('#mytab').find('input:file').length;
	if(cnt == 4){
	alert('You have upload maximum four file in Attachment');
	}
        if (cnt < 4){

var root=document.getElementById('mytab').getElementsByTagName('tr')[0].parentNode;
var oR = cE('tr');var oC = cE('td');var oI = cE('input'); var oS=cE('span');
cA(oI,'type','file');cA(oI,'name','file[]');cA(oI,'style','overflow-x:hidden;width:260px;');cA(oI,'class','no-padding form-control col-md-3 fileupload');cA(oS,'class','btn btn-danger margin-x-10');cA(oI,'onchange','uploadfile()');
oS.style.cursor='pointer';
        }    
oS.onclick=function(){
	if(cnt == 1){
	   document.getElementById('welcomeDiv').style.display = "block";
	}	
  this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
 
}
oS.appendChild(document.createTextNode('Remove'));
oC.appendChild(oI);oC.appendChild(oS);oR.appendChild(oC);root.appendChild(oR);
}
}
}
function cE(el){
this.obj =document.createElement(el);
return this.obj

}
function cA(obj,att,val){
obj.setAttribute(att,val);
return this.obj
}
function uploadfile(){
var file_size=0;
$('.fileupload').each(function() { 
	file_size=file_size+(this.files[0].size/1048576);
	});
if(file_size > 20)
{
	alert("Attachment size can not more than 20 MB.");
}
}

</script>
<script type="Text/JavaScript">
    $(document).ready(function(){
  $('#subject').change(function(){
    $('#une').text('');
    return false;
  });
  $("#myfile1").change(function(){
  $('#uneifer').text('');
    return false;
 });
  $("#myfile2").change(function(){
    $('#uneifera').text('');
    return false;
  });
  $("#template").change(function(){
    $('#body').text('');
    return false;
  });
  $("#to").change(function(){
    $('#to').text('');
    return false;
  });
  $("#from").change(function(){
    $('#from').text('');
    return false;
  });
});
    
function regvalidate()

{   
   
 
if((document.myform.subject.value==""))
 {
  document.getElementById('une').innerHTML = "Please Enter subject";
  myform.subject.focus();
  return(false);
 }
 if((document.myform.to.value==""))
 {
  document.getElementById('to').innerHTML = "Please Enter Atleast One Value";
  myform.to.focus();
  return(false);
 }
 if((document.myform.from.value==""))
 {
  document.getElementById('from').innerHTML = "Please Enter Atleast One Value";
  myform.from.focus();
  return(false);
 }
 var file_size=0;
 $('.fileupload').each(function() { 
	file_size=file_size+(this.files[0].size/1048576);
 });
 if(file_size > 20)
 {
	alert("Attachment size can not more than 20 MB.");
	return false;
 }
 var usermessage = document.forms["myform"]["template"].value;
    if(usermessage === '')
    {
       document.getElementById('body').innerHTML = "Please Enter Message";
       document.getElementById('template').focus();
       return false;
    }
    else
    {
        return true;
    }
 }
</script>
<script>
function showDiv() {

 // var id = document.getElementById("file").value;
   document.getElementById('welcomeDiv').style.display = "block";

}
</script>
<script>
function here_hide() {
 // var id = document.getElementById("file").value;
   document.getElementById('welcomeDiv').style.display = "none";

}
</script>
<style>
table {
   
 border-radius: 10px;
 border:"1";
   }
</style>
<? endblock() ?>
<? startblock('page-title') ?>
    Compose Email <? $page_title ?>
<? endblock() ?>
<? startblock('content') ?>

<section class="slice color-three padding-">
 <div class="container">
 <form method="post" action="<?= base_url()?>email/email_client_get/" enctype="multipart/form-data" name="myform" id="myform" onsubmit="return(regvalidate())">
     <span  style="margin-left:10px; text-align: center;background-color: none;color:#DD191D;">
                    <? if(isset($error) && !empty($error)) {
						echo $error;
					}?>
                 </span>
 
   <div class="col-md-12 no-padding margin-t-15 margin-b-10">
	        <div class="col-md-10"></div>
	  </div>
     <div class="w-box padding-b-10 padding-t-10" >
         <div style="margin-left:225px;">
 <font color='red'> <DIV id="from"> </DIV> </font>
 </div>   
     <li class="col-md-12">
     <label class="col-md-2 no-padding">From *</label>
<div class="col-md-5 no-padding" height='10'>

    <input  class="form-control" type="input" name="from" value="<?= $from   ?>" size="80" id="from"  style="height:33px;"/> 
 <div class="col-md-12 no-padding error_div">&nbsp;
 <div class="col-md-12">&nbsp;</div>

 </div>
</div>
</li>    
   
  <li class="col-md-12"><label class="col-md-2 no-padding">To *</label>
<div class="col-md-5 no-padding" height='10'>
<span>
<textarea name = 'to'  size = '' class = "form-control" cols="40" rows="3"  style="width: 523px; height: 90px;"><?php echo $to  ?></textarea>  
 <div class="col-md-12 no-padding error_div">&nbsp;
 <div class="col-md-3">&nbsp;</div>
 
 </div></li> 
 <div style="margin-left:225px;">
 <font color='red'> <DIV id="to"> </DIV> </font>
 </div>
 <li class="col-md-12"><label class="col-md-2 no-padding">Subject *</label>
 <span class="no-padding col-md-5">
     <input type="text" name="subject" value="<?= $subject   ?>" id="subject" size="80" value="" maxlength="200"class="form-control" style="height:33px;" />
  
 <div class="col-md-12 no-padding error_div">
     
 <div class="col-md-3">&nbsp;</div>

 </div></li>
 <div style="margin-left:225px;">
 <font color='red'> <DIV id="une"> </DIV> </font>
 </div>
  <div style="margin-left:225px;">
 <font color='red'> <DIV id="body" class="Emailtemplate"> </DIV> </font>
 </div>
 <div>
  <li class="col-md-1 " >
 <label class="col-md-2 no-padding">Message </label>
<div style="margin-left :209px;">
<textarea input name = 'template' id = 'template' size = 0 class = "Emailtemplate"  ><?=  $template  ?></textarea>
 <div class="col-md-12 no-padding error_div">&nbsp;
 <div class="col-md-3">&nbsp;</div>
 </div>
 </div></li>   
 
      <input type="hidden" name="temp" value="<?= $temp   ?>">
 </div>
  <li class="col-md-12 padding-t-10" >
       <label class="col-md-2 no-padding" style="margin-right:10px" >Attach files: </label>
      
      
       <div style="margin-left:100px">
   
	<table id="mytab">
	 <tr>
         <td bgcolor="#FFFFFF"><span class="no-padding form-control col-md-12"  style="overflow-x:hidden;width:260px;">
                 <input type="file" name="file[]"  id="file" class="text field large upload_input fileupload"  style="height:32px; margin-left: 0px; " onclick="showDiv()" onchange='uploadfile()'>
                <input type="hidden" name="add_new" id="add_new" value="0">            
             </span>
             <span id="welcomeDiv"  style="display:none; margin-left:270px;" class="answer_list" onclick="here_hide();">
	  <button type="reset" title="Cancel" onClick="document.getElementsById('file').value=''" class="btn btn-danger " style="cursor: pointer;">Remove</button>
		
	</span>
             </td>
	     <td>
	
	    </td>
		</tr>
                   
</table>
            <div style="margin-left:100px">
 <span onclick="addFile()" style="cursor:pointer; margin-left: 30px" class='btn btn-line-parrot pull-center  margin-y-10'>Attach files</span> 
       </div>
       </div>               
  </li>
  
  <div  class="col-md-12">
     <div > <label class="col-md-5 no-padding"> </label></div>
     <div class="col-md-5  ">
         <a href='".base_url()."email/email_client_get/'><input type='submit' class='btn btn-line-parrot pull-center  margin-y-10'  value='Send'/></a>
         <a href='/email/email_mass/'><input type='button' class='btn btn-line-sky pull-center  margin-x-10'  value='Cancel'/></a>
     </div>
 </div>
      <div class="col-md-7 col-lg-offset-3 padding-t-10" >
          <div class="container">
          <table align="center" style="width:100%" class="table table-bordered">
		<tr>
               
		</tr>
           
                
                <tr>
                    <td>   KEY</td><td>  VALUE</td>
		</tr>
                
		<tr>
                    <td>#NAME# </td><td> This tag use to print Firstname + Lastname  </td>
		</tr>
                <tr>
                    <td>#USERNAME# </td><td> This tag use to print user number </td>
		</tr>
		<tr>
                    <td>#PASSWORD# </td><td> This tag use to print password</td>
		</tr>
		<tr>
		    <td>#COMPANY_EMAIL# </td><td>This tag use to print company email id</td>
		</tr>
            
		<tr>
                    <td>#COMPANY_NAME#  </td><td>This tag use to print company name</td>
		</tr>
		<tr>
                    <td>#BALANCE# </td><td>This tag use to print user balance</td>
		</tr>
		<tr>
                    <td>#COMPANY_WEBSITE# </td><td>This tag use to print company website link</td>
		</tr>
		<tr>
                    <td>#PIN# </td><td>This tag use to print user pin numbner</td>
		</tr>
	</table>
          </div>              
          
      </div>    
      
 </div>
  </div>
     
     
  
     
     
 </form>
</section>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
