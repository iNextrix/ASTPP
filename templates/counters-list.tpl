<table class="default">
      <tr class="header">
	<td colspan=10>List Package Counters with time in them</td>
      </tr>
      <tr class="header">
        <td>ID</td>
        <td>Package Name</td>
	<td>Account Name</td>
        <td>Seconds Used</td>
      </tr>
      <TMPL_LOOP NAME="counter_list">
            <TR>
               <TD><TMPL_VAR NAME="id"></TD>
	       <TD><TMPL_VAR NAME="package"></TD>
               <TD><TMPL_VAR NAME="account"></TD>  
               <TD><TMPL_VAR NAME="seconds"></TD>         
            </TR>
      </TMPL_LOOP> 
</table>

<table>
      <tr>
	<td><TMPL_VAR NAME="pagination"></td>
      </tr>
<tr>
        <td>
                <TMPL_VAR NAME="status">
        </td>
</tr>
  </table>
</form>
