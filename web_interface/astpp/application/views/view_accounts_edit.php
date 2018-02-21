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
<input name="mode" value="Edit Account" type="hidden">
<table width='40%'>
<tr class="header">
	<td colspan=3>Please select the account you wish to remove</td>
</tr>
<tr class='rowone'>
	<td width="10%">
		<select name="accountlist_menu"><?=$accountlist?></select>
	</td>
	<td width="10%" >
		<input name="number" size="20" type="text">
	</td>
	<td >
		<input name="action" value="Update Account" type="submit">
	</td>
</tr>
<tr>
        <td colspan='3' align='center'>
                <?=$status?>
        </td>
</tr>

</table>
</form>

<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>



<?php 
	//echo $form;
?>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
