Create CallShop Booth<br>
<form method="post"
 action="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=Create%20Booth"
 enctype="multipart/form-data">
  <table class="default">
    <tbody>
      <tr class="header">
        <td>Booth Name</td>
        <td>Password</td>
        <td>Pricelist</td>
        <td colspan="1">Credit Limit in <TMPL_VAR NAME="default_currency"></td>
        <td>Currency</td>
      </tr>
      <tr class="rowone">
        <td>
<input name="mode" value="Create Booth" type="hidden">
<input name="accounttype" value="6" type="hidden">
<input name="sweep" value="0" type="hidden">
<input name="number" size="20" type="text">
</td>
        <td><input name="accountpassword" size="20" type="password"></td>
        <td><TMPL_VAR NAME="pricelists"></td>
        <td><input name="credit_limit" size="6"type="text"></td>
        <td><TMPL_VAR NAME="currency"></td>
	<td></td>
      </tr>
      <tr class="header">
        <td>Post Paid</td>
        <td>Dialed Number Mods</td>
        <td>IP Address</td>
        <td>Add Device</td>
        <td>Language</td>
        <td>Device Context</td>
      </tr>
      <tr class="rowtwo">
        <td><select name="posttoexternal"><option value="0">NO</option><option selected="selected" value="1">YES</option></select></td>
        <td><input name="dialed_modify" size="20" type="text"></td>
        <td><input name="ipaddr" value="dynamic" size="20" type="text"></td>
        <td><label><input name="SIP" value="on" type="checkbox">SIP</label><label><input name="IAX2" value="on" type="checkbox">IAX2</label></td>
        <td><TMPL_VAR NAME="language"></td>
	<td><input name="context" value="<TMPL_VAR NAME="context">" type="text"></td>
      </tr>
      <tr class="header">
        <td><input name="action" value="Generate Booth"
 type="submit"></td>
        <td></td>
        <td></td>
        <td><br>
        </td>
        <td></td>
      </tr>
      <tr>
      </tr>
    </tbody>
  </table>
  <table>
      <tr>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>

