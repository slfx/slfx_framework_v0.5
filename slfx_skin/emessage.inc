

This is a multi-part message in MIME format.

--<%boundary%>
Content-Disposition: inline
Content-Transfer-Encoding: 7bit
Content-Type: text/plain; charset=ISO-8859-1

<%message%>

--<%boundary%>
Content-Disposition: inline
Content-Transfer-Encoding: 7bit
Content-Type: text/html; charset=ISO-8859-1

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> </title>
<style>
html, body { margin: 0; padding: 0; width: 100%; height: 100%; background: #fefefe; font-family: "Lucida Grande", "Lucida Sans Unicode", Arial, Verdana, sans-serif;font-size: 12px; color:#888 }
a {text-decoration:none; color:#114c89;}
a:hover {color:#e9830f;}
.container {width:100%;font-size: 12px;}
hr {border: 0; width: 100%; height: 1px; background: #ddd}
h4 {font-size:18px;font-weight:normal;padding:0;margin:0;color:#666 }
.frame { margin: 0 20px 10px 20px; padding: 0; width: 590px; height: auto; background: transparent;font-size: 11px; color:#000 }
.frame .top { margin: 0; height: 30px; }
.frame .mid { margin: 0; padding: 10px 0; width: 590px; height: auto;  }
.frame .btm { margin: 0; padding: 0; width: 590px; height: 10px; background: transparent; clear: both }
.content { padding: 10px;}

.actioncall {padding:10px; background: #e1e1e1 }
<%css_add%>
</style>
<!--// Notice: If this text is displayed, your email client cannot displa=
y properly the format we've sent you. You may want to consider upgrading to=
 a more recent version of your email client. //-->
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table class="container" border="0" cellpadding="0" cellspacing="0">
  <tr>
  	<td class="">
		<a href="http://domain.com">domain</a>
	</td>
  </tr>
</table>
<table class="frame" border="0" cellpadding="0" cellspacing="0">
  <tr>
  	<td class="frame top">
	</td>
  </tr>
  <tr>
	<td class="frame mid">
	 		<!--// Title //-->
	 		<h4><%title%></h4>
	 		<hr />
			<br />
			<div class="content">
			<!--// Body //-->
			<%html_message%>
			</div>

			<br />

	</td>
  </tr>
  <tr>
  	<td class="frame btm">
  	</td>
  </tr>
</table>
<br /><br /><br /><br />
<table class="container" border="0" cellpadding="0" cellspacing="0">
  <tr>
  	<td>
		<%unsubscribe%>
	</td>
  </tr>
</table>
<br />
<br />
</body>
</html>

--<%boundary%>--
