<table class="default">
      <tr class="header">
	<td colspan=10>Configuration Details</td>
<input name="mode" value="Configuration" type="hidden">
      </tr>
      <tr class="header">
	<td>ID</td>
        <td>Reseller</td>
        <td>Brand</td>
	<td>Name</td>
        <td>Value</td>
        <td>Comment</td>
	<td>Action</td>
      </tr>
      <tr class="header">
        <td colspan=7 align="center">Add / Edit Item</td>
      </tr>
      <tr class="rowone">
	<input name="id" value="<TMPL_VAR NAME="id">" type="hidden">
        <td><TMPL_VAR NAME="id"></td>
        <td><TMPL_VAR NAME="resellers"></td>
        <td><TMPL_VAR NAME="brands"></td>
        <td><input name="name" size="20" type="text" value="<TMPL_VAR NAME="name">"></td>
        <td><input name="value" size="20" type="text"  value="<TMPL_VAR NAME="value">"></td>
        <td><input name="comment" size="50" type="text"  value="<TMPL_VAR NAME="comment">"></td>
        <td><TMPL_VAR NAME="action"></td>
      </tr>
      <tr class="header">
        <td colspan=7>List</td>
      </tr>
      <TMPL_LOOP NAME="configuration_list">
            <TR>
               <TD><TMPL_VAR NAME="id"></TD>
               <TD><TMPL_VAR NAME="reseller"></TD>
	       <TD><TMPL_VAR NAME="brand"></TD>
               <TD><TMPL_VAR NAME="name"></TD>  
               <TD><TMPL_VAR NAME="value"></TD>         
               <TD><TMPL_VAR NAME="comment"></TD>         
               <TD><a href="astpp-admin.cgi?mode=Configuration&action=Delete&id=<TMPL_VAR NAME="id">">Delete</a>
                   <a href="astpp-admin.cgi?mode=Configuration&action=Edit&id=<TMPL_VAR NAME="id">">Edit</a></TD>
            </TR>
      </TMPL_LOOP> 
</table>

<table>
      <tr>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>
