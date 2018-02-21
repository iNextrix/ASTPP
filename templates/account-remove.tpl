<input name="mode" value="Remove Account" type="hidden">
<table class="default">
<tr class="header">
	<td colspan=3>Please select the account you wish to remove</td>
</tr>
<tr>
	<td>
		<TMPL_VAR NAME="accountlist_menu">
	</td>
	<td>
		<input name="number" size="20" type="text">
	</td>
	<td>
		<input name="action" value="Remove Account" type="submit">
	</td>
</tr>
<tr>
        <td>
                <TMPL_VAR NAME="status">
        </td>
</tr>
</table>
</form>
