<input name="mode" value="View Booth" type="hidden">
<TMPL_VAR NAME="force_booth_name">
<table class="default">
<tr class="header">
	<td colspan=3>Please select the booth you wish to view</td>
</tr>
<tr>
	<td>
		<TMPL_VAR NAME="booths">
	</td>
	<td>
		<input name="booth_name" size="20" type="text" default="<TMPL_VAR NAME="booth_name">">
	</td>
	<td>
		<input name="action" value="View Booth" type="submit">
	</td>
</tr>
</table>

View Booth<br>
Booth Name: <TMPL_VAR NAME="booth_name">
<table class="default">
	<tr class="header">
		<td>Balance</td>
		<td>Unrated CDRs</td>
		<td colspan=10 align=center>Actions</td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="balance"></td>
		<td><TMPL_VAR NAME="unrated_cdrs"></td>
		<td colspan=10 align=center>
		<input name="action" value="Generate Invoice" type="submit">
		<input name="action" value="Remove CDRs" type="submit"></td>
	</tr>
	<tr class="header">
       		 <td>UniqueID</td>
       		 <td>Date & Time</td>
        	<td>Caller*ID</td>
        	<td>Called Number</td>
        	<td>Disposition</td>
        	<td>Billable Seconds</td>
        	<td>Charge</td>
        	<td>Credit</td>
        	<td>Notes</td>    
        	<td>Cost</td>    
		<td>Profit</td>
      </tr>
      <TMPL_LOOP NAME="cdr_list">
            <TR>
               <TD><TMPL_VAR NAME="uniqueid"></TD>
               <TD><TMPL_VAR NAME="callstart"></TD>
               <TD><TMPL_VAR NAME="callerid"></TD>           
               <TD><TMPL_VAR NAME="callednum"></TD>
               <TD><TMPL_VAR NAME="disposition"></TD>
               <TD><TMPL_VAR NAME="billseconds"></TD>
               <TD><TMPL_VAR NAME="debit"></TD>
               <TD><TMPL_VAR NAME="credit"></TD>
               <TD><TMPL_VAR NAME="notes"></TD>
               <TD><TMPL_VAR NAME="cost"></TD>
               <TD><TMPL_VAR NAME="profit"></TD>
             </TR>
      </TMPL_LOOP>
</table>
<table>
	<tr class="header">
		<td colspan=4>VOIP Connection Info</td>
	</tr>
	<tr class="header">
		<td>SIP Username</td>
		<td>SIP Password</td>
		<td>IAX2 Username</td>
		<td>IAX2 PAssword</td>
	</tr>
	<tr>
               <TD><TMPL_VAR NAME="sip_username"></td>
               <TD><TMPL_VAR NAME="sip_password"></td>
               <TD><TMPL_VAR NAME="iax2_username"></td>
               <TD><TMPL_VAR NAME="iax2_password"></td>
	</tr>
</table>
<table>
      <tr>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
</table>

</form>
