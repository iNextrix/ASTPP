<div align="center">
  <table width="100%" border="0">
    <tr>
      <td width="50%">VOIP Wholesale Company</td>
      <td><div align="right">www.astpp.org</div></td>
    </tr>
    <tr>
      <td width="50%">123 Street</td>
      <td><div align="right">(800) 555-1212</div></td>
    </tr>
    <tr>
      <td width="50%">City, Province</td>
      <td><div align="right">support@astpp.org</div></td>
    </tr>
    <tr>
      <td width="50%">Canada</td>
      <td><div align="right"></div></td>
    </tr>
  </table>
  <hr />
  <p>I N V O I C E
</p>
</div>

<div align="left">
<table border="0">
<tr><td>
To:
</td></tr>
<tr><td>
<TMPL_VAR NAME="firstname"> <TMPL_VAR NAME="lastname">
</td></tr>
<tr><td>
<TMPL_VAR NAME="company_name">
</td></tr>
<tr><td>
<TMPL_VAR NAME="address_1">
</td></tr>
<tr><td>
<TMPL_VAR NAME="address_2">
</td></tr>
<tr><td>
<TMPL_VAR NAME="address_3">
</td></tr>
<tr><td>
<TMPL_VAR NAME="city">, <TMPL_VAR NAME="province">  <TMPL_VAR NAME="postal_code">
</td></tr>
<tr><td>
<TMPL_VAR NAME="country"></pre>
</td></tr>
</table>
</div>
<table width="100%" border="1" cellspacing="0">
  <tr>
    <td width="33.3%"><div align="center">Account ID</div></td>
    <td width="33.3%"><div align="center">Invoice Number</div></td>
    <td width="33.3%"><div align="center">Invoice Date</div></td>
  </tr>
  <tr>
    <td><div align="center"><TMPL_VAR NAME="accountid"></div></td>
    <td><div align="center"><TMPL_VAR NAME="invoiceid"></div></td>
    <td><div align="center"><TMPL_VAR NAME="invoicedate"></div></td>
  </tr>
</table>
<p>&nbsp;</p>
<table width="100%" class="default">
      <tr class="header">
        <td width="20%">Date & Time</td>
        <td width="20%">Caller*ID</td>
        <td width="20%">Called Number</td>
        <td width="20%">Disposition</td>
        <td width="10%">Duration</td>
        <td width="10%">Charge</td>
      </tr>
      <TMPL_LOOP NAME="invoice_cdr_list">
            <TR>
               <TD><TMPL_VAR NAME="callstart"></TD>
               <TD><TMPL_VAR NAME="callerid"></TD>
               <TD><TMPL_VAR NAME="callednum"></TD>
               <TD><TMPL_VAR NAME="disposition"></TD>
               <TD><TMPL_VAR NAME="billseconds"></TD>
               <TD><div align="right"><TMPL_VAR NAME="charge"></div></TD>
             </TR>
      </TMPL_LOOP>
</table>
<br>
<table width="100%" class="default">
      <tr class="header">
        <td width="40%"></td>
        <td width="20%">Title</td>
        <td width="20%">Text</td>
        <td width="20%">Fee</td>
      </tr>
      <TMPL_LOOP NAME="invoice_total_list">
            <TR>
	       <TD></td>
               <TD><TMPL_VAR NAME="title"></TD>
               <TD><TMPL_VAR NAME="text"></TD>
               <TD><div align="right"><TMPL_VAR NAME="value"></div></TD>
             </TR>
      </TMPL_LOOP>
</table>
