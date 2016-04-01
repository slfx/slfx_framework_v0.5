<!DOCTYPE html>
<html lang="en" <%page_class%>>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><%page_title%></title>
<%meta%>
<%head_include%>
</head>
<body <%page_class%>>

	<div class="container-fluid" id="header">
    <div class="row">
        <div class="col-xs-12 col-sm-3">
            <a href="/" id="logo" class="hover-fade add_active nukedev top light"><strong>Company</strong><span class="blue">Name</span> <span class="hide"> <%page_title%> </span> </a>
        </div>

        <div class="col-xs-12 col-sm-9">
            <ul class="nav" id="nav-main">
                <li><a href="/" id="home_btn" class="nav_sub clickable add_active nukedev top light">Home</a></li>

                <li>
                    <ul id="about_menu" class="drop_menu">
                        <li class="open">
                            <a id="about_btn" class="protect drop_button nav_sub" href="#" onclick="return false">About</a>

                            <ul class="sublist" id="products_drop">
                                <li><a href="/about/team" id="team_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">Team</a></li>

                                <li><a href="/about/services" id="services_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">Services</a></li>

                            </ul>
                        </li>
                    </ul>
                </li>
                <li><span id="contact_btn" class="nav_sub clickable add_active">Contact</span></li>
            </ul>
        </div>
    </div>
</div>
<div id="main">
    <div id="content">
        <div id="ajax">
            <%content%>
        </div>

        <div class="box" id="url_buffer">
            <h1>URL Buffer</h1>

            <div>
                <p>The following is a change log of URL updates:</p>

                <ol id="log">
                    <!-- log - injected -->
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid gray-super-light-bg" id="footer">
    <div class="row" style="padding-top: 72px;">
        <div class="col-xs-12 col-sm-6 col-md-3 col-sm-push-6 col-md-push-9 text-left-xs text-right-sm wordage" style="padding-left:30px; padding-right: 40px; ">
		    <a class="nukedev dark small"><strong>Company</strong><span class="blue">Name</span></a><br />
		    <br />
		    Planetarium Station<br />
		    PO Box 522<br />
		    New York, NY 10024-0522<br />
		    <br />
		    <br />
		    <a href="mailto:general@domain.com">general@domain.com</a><br />
		    212 .867 .5309<br />
		    <br />

		    <div class="made-in-usa">
		        Made in USA
		    </div>
		    <br />

		</div>

		<div class="col-xs-12 col-sm-6 col-md-9 col-sm-pull-6 col-md-pull-3">
			<div class="row">

			   	<div class="col-xs-12 col-sm-12 col-md-2 wordage" style="padding-left:40px; padding-right: 20px; ">
			        <span id="social"><a href="https://twitter.com/" class="social-btn" id="twitter" target="_blank"></a><!-- <a href="https://plus.google.com/u/0/b/101598548313466261471/101598548313466261471/about" class="social-btn" id="googleplus" target="_blank"></a>--><a href="https://www.facebook.com/" class="social-btn" id="facebook" target="_blank"></a><br /><br /></span>
			   	</div>
			   	<div class="col-sm-12 col-md-3 wordage" style="padding-left:20px; padding-right: 20px; ">
			        <strong>Services</strong><br />
			        <a href="/1" class="">service 1</a><br />

			   	</div>

			    <div class="col-sm-12 col-md-3 wordage" style="padding-left:20px; padding-right: 20px; ">
			        <strong>Recent Clients</strong><br />
			        <a href="/2" class="">Client 1</a><br />
					</div>

			    <div class="col-sm-12 col-md-3 wordage" style="padding-left:20px; padding-right: 20px; ">
			        <strong>Coding Projects</strong><br />
			        <a href="/works/coding/url-shortener" class="">Project 1</a><br />

			    </div>

			</div>
		</div>


    </div>

    <div class="row">
        <div class="col-md-12 wordage">
            <span id="atom"></span><a href="/tos">Terms of Service</a> and <a href="/privacy">Privacy Policy</a>. &copy;<%year%>.
        </div>
    </div>
	<%foot_include%><%load_time%>
</div>

</body>
</html>
