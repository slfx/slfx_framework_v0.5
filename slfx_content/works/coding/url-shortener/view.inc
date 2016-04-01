<div class="container-fluid">
	<div class="row parallax parallax-1" style="background-image: <%parallax-image%>">
		<div class="col-xs-12">
			<h1 class="top">URL Shortener</h1>
			<p class="top">This is a fast easy way to transform a web address into a convenient short url which you can use social networking tools.</p>
		</div>		
	</div>
	<div class="row parallax-2-backdrop" style="background-image: <%parallax-image%>">
		<div class="parallax parallax-2"  style="background-image: <%parallax-image%>"></div>
	</div>
</div>
<div class="container-fluid white-bg">
	<div class="row white-bg">
		<%navsub%>
		<div class="col-xs-12" style="padding-bottom: 60px;padding-top: 120px;">

				<form method="post" action="/works/coding/url-shortener/" id="short">
					<label for="longurl">enter URL to be shortened</label> <br />
					<input type="text" name="url" id="url" class="" value="http://">
					<input type="submit" value="shorten" class="btn" />
				</form>
				<span class="smaller">
					<span id="help">
						<a href="#" id="nohelp" class="hide" onclick="showThis('gohelp', 'nohelp');showThis('','helper');"> hide help</a>
						<span onclick="showThis('nohelp','gohelp');showThis('helper');" id="gohelp"> help</span>
						<div class="hide" id="helper">
							Get a shortlink from right from the url:<br />
							<code>
								http://www.nucleusdevelopment.com/works/coding/url-shortener?url=http:%2f%2fYOUR_URL
							</code><br />
							Dynamically create shortlinks in php code:<br />
							<code>
								&lt;?php<br />
								http://www.nucleusdevelopment.com/works/coding/url-shortener/?url='. urlencode( $long_link ) );<br />
								?&gt;
							</code>
						</div>
					</span>
					<div class="right width300px textright hide" id="sharelinks">
						share this shortlink:
						<div id="social">
							<a target="_blank" href="http://twitter.com/intent/tweet?text=" id="tw"><strong>twitter</strong></a>
							<a target="_blank" href="https://www.facebook.com/sharer.php?u=" id="fb"><strong>facebook</strong></a>
						</div>
					</div>
					<br />
					<br />
					this page's shortlink: <a href="http://nkdv.co/<%INFO_PAGE_SHORTNAME%>" target="_blank">http://nkdv.co/<%INFO_PAGE_SHORTNAME%></a>
				</span>
		</div>
	</div>
	<%code-block%>
</div>
<script>
		$(function () {
		$('#short').submit(function () {
		$.ajax({data: {url: $('#url').val()}, url: '/works/coding/url-shortener/', complete: function (XMLHttpRequest, textStatus) {
		$('#url').val(XMLHttpRequest.responseText);
		if(XMLHttpRequest.responseText == 'Enter a URL starting with http://') {
			$('#sharelinks').fadeOut(200);
			return false;
		}
		$('#tw').attr('href', 'http://twitter.com/intent/tweet?text='+XMLHttpRequest.responseText);
		$('#fb').attr('href', 'https://www.facebook.com/sharer.php?u='+XMLHttpRequest.responseText);
		$('#sharelinks').fadeIn(200);}});return false;
		});
		});
		function showThis(a1, b1) {
		var a = document.getElementById(a1);
		var b = document.getElementById(b1);
		if (a != null) a.style.display = 'block';
		if (b != null) b.style.display = 'none';
		return;
		}
</script>
