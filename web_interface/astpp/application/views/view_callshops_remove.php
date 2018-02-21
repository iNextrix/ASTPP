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

<form method="post" action="." enctype="multipart/form-data">
<input name="mode" value="Remove CallShop" type="hidden">
<table class="default" width='40%'>
<tr class="header">
	<td colspan=3>Please select the callshop you wish to view</td>
</tr>
<tr>
<td width="10%">Call Shops</td>
<td><select name="callshop_list" >
<?=$callshop_list?>
</select></td>
</tr>

<tr>
<td>&nbsp;</td>
<td><input name="action" value="Remove CallShop" type="submit"></td>
</tr>

<!-- 

<tr class='rowone'>
	<td>
		<TMPL_VAR NAME="callshop_list">
		
	</td>
	<td>
		<input name="callshop" size="20" type="text">
	</td>
	<td>
		<input name="action" value="Remove CallShop" type="submit">
	</td>
</tr>
<tr>
	<td>
		<TMPL_VAR NAME="status">
	</td>
</tr>
<tr><td><br/><br/><br/></td></tr>
 -->
</table>
</form>



<?php 
	//echo $form;
?>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
