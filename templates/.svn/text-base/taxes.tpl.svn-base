<table class="default">
      <tr class="header">
	<td colspan=10 align="center">Tax Details</td>
<input name="mode" value="Taxes" type="hidden">
      </tr>
      <tr class="header">
	<td>ID</td>
        <td>Priority</td>
        <td>Amount</td>
	<td>Rate</td>
        <td>Description</td>
        <td>Modified</td>
	<td>Created</td>
	<td>Action</td>
      </tr>
      <tr class="header">
        <td colspan=8 align="center">Add / Edit Item</td>
      </tr>
      <tr class="rowone">
	<input name="taxes_id" value="<TMPL_VAR NAME="taxes_id">" type="hidden">
        <td><TMPL_VAR NAME="taxes_id"></td>
        <td><input name="taxes_priority" size="4" type="text" value="<TMPL_VAR NAME="taxes_priority">"></td>
        <td><input name="taxes_amount" size="20" type="text" value="<TMPL_VAR NAME="taxes_amount">"></td>
        <td><input name="taxes_rate" size="20" type="text" value="<TMPL_VAR NAME="taxes_rate">"></td>
        <td><input name="taxes_description" size="50" type="text"  value="<TMPL_VAR NAME="taxes_description">"></td>
        <td><TMPL_VAR NAME="last_modified"></td>
        <td><TMPL_VAR NAME="date_added"></td>
        <td><TMPL_VAR NAME="action"></td>
      </tr>
      <tr class="header">
        <td colspan=8>List</td>
      </tr>
      <TMPL_LOOP NAME="taxes_list">
            <TR>
               <TD><TMPL_VAR NAME="taxes_id"></TD>
               <TD><TMPL_VAR NAME="taxes_priority"></TD>
	       <TD><TMPL_VAR NAME="taxes_amount"></TD>
               <TD><TMPL_VAR NAME="taxes_rate"></TD>  
               <TD><TMPL_VAR NAME="taxes_description"></TD>         
               <TD><TMPL_VAR NAME="last_modified"></TD>         
               <TD><TMPL_VAR NAME="date_added"></TD>         
               <TD><a href="astpp-admin.cgi?mode=Taxes&action=Delete&taxes_id=<TMPL_VAR NAME="taxes_id">">Delete</a>
                   <a href="astpp-admin.cgi?mode=Taxes&action=Edit&taxes_id=<TMPL_VAR NAME="taxes_id">">Edit</a></TD>
            </TR>
      </TMPL_LOOP> 
</table>

<table>
      <tr>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>
