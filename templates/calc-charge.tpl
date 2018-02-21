<table class="default">
<input type="hidden" name="mode" value="Calc Charge"  />
      <tr class="header">
	<td colspan=5>Add / Edit / List Packages</td>
      </tr>
      <tr class="header">
		<td>Phone Number</td>
		<td>Length (Minutes)</td>
		<td>Pricelist</td>
		<td>Action</td>
	</tr>
	<tr class="rowone">
		<td><input type="text" name="phonenumber"  size="20" /></td>
		<td><input type="text" name="length"  size="6" /></td>
		<td><TMPL_VAR NAME="pricelists"></td>
		<td><input type="submit" name="action" value="Price Call..." /></td>
	</tr></table>

<table>
      <tr>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>
