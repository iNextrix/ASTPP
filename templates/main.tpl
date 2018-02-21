<link rel=icon type=image/png href=/_astpp/favicon.ico>
<link rel="stylesheet" type="text/css" href="/_astpp/style.css">
<title>ASTPP - Open Source VOIP Billing Admin</title>
<script src ="/_astpp/menu.js" type="text/javascript"> </script>

<BODY>
<center>
<table width=100% class="default" cellpadding=5 cellspacing=5 border=3>
<tr><td align=center>
	<img src="<TMPL_VAR NAME= "company_logo">">
</td><td align=right bgcolor=aaaaee>
 	<b><TMPL_VAR NAME="username">@<TMPL_VAR NAME="host"></b>
	(<TMPL_VAR NAME="logintype">)
	| settings
	| <a href=http://www.astpp.org target=astpp>help</a>
	| <a href=/cgi-bin/astpp/astpp-users.cgi>usermode</a>
	| <a href=/cgi-bin/astpp-admin/astpp-admin.cgi>adminmode</a>
	| <a href=/cgi-bin/astpp-admin/astpp-admin.cgi?mode=Logout>logout</a>
	<br>
	<b><TMPL_VAR NAME="mode"></b> : Admin | ASTPP
</td></tr>

<tr><td colspan=2 align=center>
	<TMPL_VAR NAME="menu">
</tr>
<tr><td colspan=2 align=center>
<br>
	<TMPL_VAR NAME="status">
<br>
	<TMPL_VAR NAME="body">
</td></tr>
<tr><td colspan=2 align=center bgcolor=bbccff>
<COPYRIGHT>
<small>
	<a href="<TMPL_VAR NAME="company_website">" target=company><TMPL_VAR NAME="company_name"></a> <i><TMPL_VAR NAME="company_slogan">!</i>
	<br>
	<a href=http://www.astpp.org target=astpp>ASTPP</a>-v<TMPL_VAR NAME="version"> &copy 2006 Aleph Communications <i> 100% Open Source VoIP Billing!!</i>
	<br>Server took <TMPL_VAR NAME="time_gen"> secs on <TMPL_VAR NAME="time_now"> optimized for <a href="http://getfirefox.com">FireFox</a> @ 1024x768 resolution 
</small>
</COPYRIGHT>
</td></tr>
</table>
</center>
</BODY>

