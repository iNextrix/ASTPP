
<link href="<?= base_url() ?>assets/css/popup.css" rel="stylesheet" type="text/css">
 <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/popup/custom.js"></script>
<script type="text/javascript">
function validateForm(){
      if((document.form1.name.value==""))
       {
     document.getElementById('une').innerHTML = "Please enter name";
   form1.name.focus();
  return(false);
      }
      if((document.form1.email.value==""))
       {
  document.getElementById('email1').innerHTML = "Please enter email";
  form1.email.focus();
  return(false);
      }
       
      
      var email = document.getElementById('email');
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (!filter.test(email.value)) {
   document.getElementById('email2').innerHTML = "Please enter proper email address";
    email.focus;
    return false;
    }
      if((document.form1.feedback.value==""))
       {
  document.getElementById('feedback1').innerHTML = "Please enter feedback";
  form1.feedback.focus();
  return(false);
      }
      
     function checkEmail() {

    
    }
 }
 $(document).ready(function(){
  $('#name').change(function(){
    $('#une').text('');
    return false;
  });
   $('#email').change(function(){
    $('#email1').text('');
    return false;
  });
  $('#email').change(function(){
    $('#email2').text('');
    return false;
  });
  $('#feedback').change(function(){
    $('#feedback1').text('');
    return false;
  });
  });
</script>
<html>
<title>ASTPP-Feedback</title>
<div id="form-main">
  <div id="form-div">
  <center><h1><b>Feedback</b><h1></center>
    <form class="form" id="form1" name="form1" method="post" action="<?=base_url()?>feedback/customer_feedback_result">
      <p>Thank you for taking the time to fill in feedback form. By providing us your feedback or suggestion, you are helping us understand what we do well and what improvements we need to implement.</p>
      <div class="name">
        <input name="name" type="text" class=" feedback-input" placeholder="Name" id="name" />
        <span id="une" style=""> </span> 
      </div>
      <div class="email">
        <input name="email" type="text" class="validate[required,custom[email]] feedback-input" id="email" placeholder="Email" />
      </div>
       <font color='red'> <span id="email1" name="email1"> </span> </font>
        <font color='red'> <span id="email2" name="email1"> </span> </font>
      <div class="text">
     
        <textarea name="feedback" name="feedback" id="feedback" class="validate[required,length[6,300]] feedback-input" id="comment" placeholder="Feedback"></textarea>
      </div>
       <font color='red'> <span id="feedback1" name="feedback1"> </span> </font>
        <center><input type="submit" value="Feedback"  class="btn btn-line-parrot" onclick="return validateForm();"/></center>
       <input name="account_info" type="hidden" value="<?php  print_r($account_info); ?>">
       
    </form>
  </div>
  </html>
