<input type="hidden" name="mode" value="Create CallShop"/>
Please enter the following information to create a new callshop<br>
<table style="text-align: left; width: 100%;" 
 cellpadding="2" cellspacing="2">
  <tbody>
    <tr class='rowone'>
      <td style='color:white;'>Call Shop Name</td>
      <td style='color:white;'><input size="10" name="callshop_name"></td>
    </tr>
    <tr  class='rowone'>
      <td style='color:white;'>Login Password</td>
      <td style='color:white;'><input size="10" name="accountpassword"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>Credit Limit</td>
      <td style='color:white;'><input size="10" name="credit_limit"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>Sweep</td>
      <td style='color:white;'><TMPL_VAR NAME="sweep"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>Language</td>
      <td style='color:white;'><TMPL_VAR NAME="language"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>Currency</td>
      <td style='color:white;'><TMPL_VAR NAME="currency"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>Link to OSCommerce Site</td>
      <td style='color:white;'><input size="60"
 value="http://www.companysite.com/store/"
 name="osc_site"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>OSCommerce Database Name</td>
      <td style='color:white;'><input name="osc_dbname"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>OSCommerce Database Host</td>
      <td style='color:white;'><input name="osc_dbhost"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>OSCommerce Database Password</td>
      <td style='color:white;'><input name="osc_dbpass" type="password"></td>
    </tr>
    <tr class='rowone'>
      <td style='color:white;'>OSCommerce Database Username</td>
      <td style='color:white;'><input name="osc_dbuser"></td>
    </tr>
  </tbody>
</table>
<br>
<input type="submit" name="action" value="Add..." />
<br><hr>
<TMPL_VAR NAME= "status">

