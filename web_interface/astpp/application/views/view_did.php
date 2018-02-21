<? extend('master.php') ?>

	<? startblock('extra_head') ?>
		
	<script type="text/javascript" src="/js/validate.js"></script>
	<script type="text/javascript">
		$().ready(function() {
		// validate signup form on keyup and submit
		$("#signupForm").validate({
			rules: {
				name: "required",
				accountno: "required",
				username: {
					required: true,
					minlength: 2
				},
				password: {
					required: true,
					minlength: 5
				},
				password1: {
					required: true,
					minlength: 5,
					equalTo: "#password"
				},
				email: {
					required: true,
					email: true
				},
				topic: {
					required: "#newsletter:checked",
					minlength: 2
				},
			messages: {
				firstname: "Please enter your firstname",
				lastname: "Please enter your lastname",
				username: {
					required: "Please enter a username",
					minlength: "Your username must consist of at least 2 characters"
				},
				password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long"
				},
				confirm_password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long",
					equalTo: "Please enter the same password as above"
				},
				email: "Please enter a valid email address",
				agree: "Please accept our policy"
			}
			}
		});
		});
	</script>
<style>
    fieldset{
        width: 609px;
    }
</style>			
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>
<br/>

<?php 
	$a = array('<foo>',"'bar'",'"baz"','&blong&', "\xc3\xa9");
	echo "Normal : ",  json_encode($a), "\n";
	$json_data = array();
	
	$json_data['page'] = 1;
	$json_data['total'] = 10;	
	$json_data['rows'][] = array('Id'=>1,'cell'=>'12122') ;
	echo "Normal : ",  json_encode($json_data), "\n";
	
?>
<form action="." id="signupForm1" method="POST" enctype="multipart/form-data">
<input type="hidden" name="mode" value="Create CallShop"/>
Please enter the following information to create a new callshop<br>
<table style="text-align: left; width: 100%;" 
 cellpadding="2" cellspacing="2">
  <tbody>
    <tr class='rowone'>
      <td >Call Shop Name</td>
      <td ><input size="10" name="callshop_name"></td>
    </tr>
    <tr  class='rowone'>
      <td >Login Password</td>
      <td ><input size="10" name="accountpassword"></td>
    </tr>
    <tr class='rowone'>
      <td >Credit Limit</td>
      <td ><input size="10" name="credit_limit"></td>
    </tr>
    <tr class='rowone'>
      <td >Sweep</td>
      <td ><TMPL_VAR NAME="sweep"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>Language</td>
      <td style='color:white;'><TMPL_VAR NAME="language"></td>
    </tr>
    <tr class='rowone'>
      <td >Currency</td>
      <td ><TMPL_VAR NAME="currency"></td>
    </tr>
    <tr class='rowone'>
      <td >Link to OSCommerce Site</td>
      <td ><input size="60"
 value="http://www.companysite.com/store/"
 name="osc_site"></td>
    </tr>
    <tr class='rowone'>
      <td >OSCommerce Database Name</td>
      <td ><input name="osc_dbname"></td>
    </tr>
    <tr class='rowone'>
      <td >OSCommerce Database Host</td>
      <td ><input name="osc_dbhost"></td>
    </tr>
    <tr class='rowone'>
      <td >OSCommerce Database Password</td>
      <td><input name="osc_dbpass" type="password"></td>
    </tr>
    <tr class='rowone'>
      <td >OSCommerce Database Username</td>
      <td><input name="osc_dbuser"></td>
    </tr>
  </tbody>
</table>
<br>
<input type="submit" name="action" value="Add..." />
<br><hr>
<TMPL_VAR NAME= "status">
</form>



<?php 
	//echo $form;
?>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
