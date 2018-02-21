<table class="default">
<input type="hidden" name="mode" value="Packages"  />
      <tr class="header">
	<td colspan=5>Add / Edit / List Packages</td>
      </tr>
      <tr class="header">
        <td>Package Name</td>
        <td>Pricelist</td>
	<td>Pattern</td>
        <td>Included Seconds</td>
        <td>Action</td>
      </tr>
	<tr class="header">
		<td colspan=5>Add Package</td>
	</tr>
	<tr>
		<td><input type="text" name="name"  size="20" /></td>
		<td><TMPL_VAR NAME="pricelists"></td>
		<td><input type="text" name="pattern"  size="30" /></td>
		<td><input type="text" name="includedseconds"  size="6" /></td>
		<td><input type="submit" name="action" value="Insert..." /></td>
	</tr>
	<tr class="header">
		<td colspan=5>Edit Package</td>
	</tr>
	<tr>
		<input type="hidden" name="id" value="<TMPL_VAR NAME="id">"  />
		<td><input type="text" name="edit_name"  size="20" value="<TMPL_VAR NAME="current_name">"/></td>
		<td><TMPL_VAR NAME="edit_pricelists"></td>
		<td><input type="text" name="edit_pattern"  size="30" value="<TMPL_VAR NAME="current_pattern">"/></td>
		<td><input type="text" name="edit_includedseconds" size="6" value="<TMPL_VAR NAME="current_includedseconds">"</></td>
		<td><input type="submit" name="action" value="Save..." /></td>
	</tr>
	<tr class="header">
		<td colspan=5>List Package</td>
	</tr>
	<tr class="header">
	</tr>
      <TMPL_LOOP NAME="package_list">
            <TR>
		<TD><TMPL_VAR NAME="name"></TD>
		<TD><TMPL_VAR NAME="pricelist"></TD>
		<TD><TMPL_VAR NAME="pattern"></TD>
		<TD><TMPL_VAR NAME="includedseconds"></TD>
		<td><a href="astpp-admin.cgi?mode=Packages&action=Edit...&id=<TMPL_VAR NAME="id">">Edit...</a>
			<a href="astpp-admin.cgi?mode=Packages&action=Deactivate...&id=<TMPL_VAR NAME="id">">Deactivate...</a></td>
	       </TD>
            </TR>
      </TMPL_LOOP> 
</table>

<table>
      <tr>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>
