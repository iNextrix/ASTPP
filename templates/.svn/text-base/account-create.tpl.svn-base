Create Account<br>
<form method="post" action="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=Create%20Account" enctype="multipart/form-data">
<table class="default">
	<tr>
		<td colspan=6><TMPL_VAR NAME="status"></td>
	</tr>
	<tr class="header">
		<td>Account Number</td>
		<td>Password</td>
		<td>Pricelist</td>
		<td>Billing Schedule</td>
		<td>Credit Limit in <TMPL_VAR NAME="default_currency"></td>
		<td>Timezone</td>
	</tr>
	<tr class="rowone">
		<td><input type="hidden" name="mode" value="Create Account"/><input type="text" name="customnum"  size="20" /></td>
		<td><input type="text" name="accountpassword"  size="20" /></td>
		<td><TMPL_VAR NAME="pricelist_menu"></td>
		<td><TMPL_VAR NAME="sweep_menu"></td>
		<td><input type="text" name="credit_limit"  size="6" /></td>
		<td><TMPL_VAR NAME="timezone_menu"></td>
	</tr>
	<tr class="header">
		<td>Post Charges to External App?</td>
		<td>First Name</td>
		<td>Middle Name</td>
		<td>Last Name</td>
		<td>Add VOIP Friend</td>
	</tr>
	<tr class="rowtwo">
		<td><select name="posttoexternal" >
			<option value="1">YES</option>
			<option selected="selected" value="0">NO</option>
			</select></td>
		<td><input type="text" name="firstname"  size="20" /></td>
		<td><input type="text" name="middlename"  size="20" /></td>
		<td><input type="text" name="lastname"  size="20" /></td>
		<td><label><input type="checkbox" name="SIP" value="on" />SIP</label><label><input type="checkbox" name="IAX2" value="on" />IAX2</label></td>
	</tr>
	<tr class="header">
		<td>Company</td>
		<td>Address 1</td>
		<td>Address 2</td>
		<td>City</td>
		<td>Context</td>
	</tr>
	<tr class="rowone">
		<td><input type="text" name="company"  size="20" /></td>
		<td><input type="text" name="address1"  size="20" /></td>
		<td><input type="text" name="address2"  size="20" /></td>
		<td><input type="text" name="city"  size="20" /></td>
		<td><input type="text" name="context" value="<TMPL_VAR NAME="default_context">" size="20" /></td>
	</tr>
	<tr class="header">
		<td>Zip/Postal Code</td>
		<td>Province/State</td>
		<td colspan=2>Country</td>
		<td>Device Type</td>
	</tr>
	<tr class="rowtwo">
		<td><input type="text" name="postal_code"  size="20" /></td>
		<td><input type="text" name="province"  size="20" /></td>
		<td colspan=2><TMPL_VAR NAME="country_menu"></td>
		<td><TMPL_VAR NAME="devicetype"></td>
	</tr>
	<tr class="header">
		<td>Telephone #1</td>
		<td>Telephone #2</td>
		<td>Fascimile</td>
		<td>Email</td>
		<td>IP Address</td>
	</tr>
	<tr class="rowone">
		<td><input type="text" name="telephone1"  size="20" /></td>
		<td><input type="text" name="telephone2"  size="20" /></td>
		<td><input type="text" name="facsimile"  size="20" /></td>
		<td><input type="text" name="email"  size="20" /></td>	
		<td><input type="text" name="ipaddr" value="dynamic" size="20" /></td>	
	</tr>
	<tr class="header">
		<td>Currency</td>
		<td>Account Type</td>
		<td>Language</td>
		<td>Max Channels</td>
		<td>Dialed Number Mods</td>
		<td>Action</td>
	</tr>
	<tr class="rowtwo">
		<td><TMPL_VAR NAME="currency_menu"></td>
		<td><TMPL_VAR NAME="accounttype_menu"></td>
		<td><TMPL_VAR NAME="languages_menu"></td>
		<td><input type="text" name="maxchannels"  size="4" /></td>
		<td><input type="text" name="dialed_modify"  size="20" /></td>
		<td><input type="submit" name="action" value="Generate Account" /></td>
	</tr>
</table>
</form>

