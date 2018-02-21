<table class="default">
      <tr class="header">
	<td colspan=10>List Booths</td>
      </tr>
      <tr class="header">
        <td>Booth Name</td>
        <td>Balance</td>
	<td>Currency</td>
        <td>Call Count</td>
        <td>In Use</td>
	<td>Duration</td>
	<td>Last Update</td>
	<td>Number</td>
	<td>Status</td>
	<td>Action</td>
      </tr>
      <TMPL_LOOP NAME="booth_list">
            <TR>
               <TD> <a href="astpp-admin.cgi?mode=View Booth&action=View Booth&booth_name=<TMPL_VAR NAME="name">"><TMPL_VAR NAME="name"></a></TD>
               <TD><TMPL_VAR NAME="balance"></TD>
	       <TD><TMPL_VAR NAME="currency"></TD>
               <TD><TMPL_VAR NAME="call_count"></TD>  
               <TD><TMPL_VAR NAME="in_use"></TD>         
               <TD><TMPL_VAR NAME="duration"></TD>         
               <TD><TMPL_VAR NAME="callstart"></TD>         
               <TD><TMPL_VAR NAME="number"></TD>         
               <TD><TMPL_VAR NAME="status"></TD>         
               <TD> <a href="astpp-admin.cgi?mode=List Booths&action=Hangup Call&channel=<TMPL_VAR NAME="channel">">Hangup Call</a>
                    <a href="astpp-admin.cgi?mode=List Booths&action=Deactivate Booth&booth_name=<TMPL_VAR NAME="name">">Deactivate Booth</a>
                    <a href="astpp-admin.cgi?mode=List Booths&action=Restore Booth&booth_name=<TMPL_VAR NAME="name">">Restore Booth</a>
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
