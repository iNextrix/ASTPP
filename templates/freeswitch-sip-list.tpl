<form method="post" action="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=Freeswitch(TM)%20SIP%20Devices" enctype="multipart/form-data">
<table>
	<tr>
		<input type="hidden" name="mode" value="Freeswitch(TM) SIP Devices"/>
		<td colspan=8 align=center><TMPL_VAR NAME="status"></td>
	</tr>
	<tr align="center" class="header">
		<td colspan=8>Add / Edit Device</td>
	</tr>
	<tr class="header">
		<td>Directory ID</td>
		<td>Username</td>
		<td>Password</td>
		<td>Accountcode</td>
		<td>VM Password</td>
		<td>Context</td>
		<td colspan=2>Action</td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="directory_id"><input type="hidden" name="directory_id" value="<TMPL_VAR NAME="directory_id">"></td>
		<TD><input name="fs_username" size="20" type="text" value="<TMPL_VAR NAME="fs_username">"></a></TD>
		<TD><input name="fs_password" size="20" type="text" value="<TMPL_VAR NAME="fs_password">"></a></TD>
		<TD><input name="accountcode" size="20" type="text" value="<TMPL_VAR NAME="accountcode">"></a></TD>
		<TD><input name="vm_password" size="20" type="text" value="<TMPL_VAR NAME="vm_password">"></a></TD>
		<TD><input name="context" size="20" type="text" value="<TMPL_VAR NAME="context">"></a></TD>
		<td colspan=2><input type="submit" name="action" value="Save..." /></td>
	</tr>
	<tr align="Center" class="header">
		<td colspan=8>Current Devices</td>
	</tr>
	<tr class="header">
		<td>Directory ID</td>
		<td>Username</td>
		<td>Password</td>
		<td>Accountcode</td>
		<td>VM Password</td>
		<td>Context</td>
		<td colspan=2>Action</td>
	</tr>
	<TMPL_LOOP NAME="device_list">
		<TR>
			<TD><TMPL_VAR NAME="directory_id"></td>
			<TD><TMPL_VAR NAME="fs_username"></a></TD>
          		<TD><TMPL_VAR NAME="fs_password"></TD>
          		<TD><TMPL_VAR NAME="accountcode"></TD>
          		<TD><TMPL_VAR NAME="vm_password"></TD>
          		<TD><TMPL_VAR NAME="context"></TD>
          		<TD><a href="astpp-admin.cgi?mode=Freeswitch(TM) SIP Devices&directory_id=<TMPL_VAR NAME="directory_id">&action=Delete...">Delete...</a></TD>
          		<TD><a href="astpp-admin.cgi?mode=Freeswitch(TM) SIP Devices&directory_id=<TMPL_VAR NAME="directory_id">&action=Edit...">Edit...</a></TD>
            </TR>
      </TMPL_LOOP>
</table>

