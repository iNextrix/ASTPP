<form method="post" action="/cgi-bin/astpp/astpp-users.cgi?mode=ANI%20Mapping" enctype="multipart/form-data">

<table class="default">
	<tr class="header">
		<td><input type="hidden" name="mode" value="ANI Mapping"  /></td>
	</tr>
	<tr>
		<td>Map ANI to Account</td>
	</tr>
	<tr>
		<td><input type="text" name="ANI" value="" width="20" /></td>
		<td><input type="submit" name="action" value="Map ANI" /></td>
	</tr>
</table>
<table class="default">
	<tr class="header">
		<td>Action</td>
		<td>ANI</td>
	</tr>
	<TMPL_LOOP NAME="ani_list">
		<tr>
			<td><a href="astpp-users.cgi?mode=ANI Mapping&ANI=<TMPL_VAR NAME="ani">&action=Remove ANI ">Remove ANI</a></td>
			<td><TMPL_VAR NAME="ani"></td>
		</tr>
	</TMPL_LOOP>
</table>
<TMPL_VAR NAME="status">
