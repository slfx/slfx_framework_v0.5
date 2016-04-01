<div class="container-fluid">
	<div class="row parallax parallax-1" style="background-image: <%parallax-image%>">
		
		<div class="col-xs-12">
			<h1 class="top">Prime Number Check</h1>
			<p class="top">This is a fast easy way to check a number to see if it is, in fact, prime. Accurate to numbers as large as 10,968,163,441.</p>
		</div>		
		
		
	</div>
	<div class="row parallax-2-backdrop" style="background-image: <%parallax-image%>">
		<div class="parallax parallax-2"  style="background-image: <%parallax-image%>"></div>
	</div>
</div>
<div class="container-fluid  white-bg">
	<div class="row white-bg">
		<%navsub%>
		<div class="col-xs-12 wordage" style="padding-bottom: 60px;padding-top: 120px;">

		</div>

		<div class="col-xs-12 col-md-7 col-md-offset-2 wordage left">
			<ul>
				<li>Check an integer for prime</li>
				<li>Check a number set for a nested prime number of any length up to 10,968,163,441.</li>
			</ul>
			<form method="post" action="/works/coding/prime-number-check/" id="check">
				<label for="longurl">enter number to be checked</label> <br />
				<input type="text" name="num" id="num" class="width500px" value="" placeholder="number">
				<input type="submit" value="check" class="btn" />
			</form>

			<span class="smaller">
				<span id="help">
					<span id="nohelp" class="hide" onclick="showThis('gohelp', 'nohelp');showThis('','helper');"> hide help</span>
					<span onclick="showThis('nohelp','gohelp');showThis('helper');" id="gohelp"> help</span>
					<div class="hide" id="helper">
						Enter any integer to check for prime. <br />
						ie: 2,231<br />
						Or enter the haystack (float or int) and the needle length (int) separated by a space.
						<code>
						<br />ei: 3.14159265358979323846264338327950288419716939937510582097494459230781640628620899862803482534211706798214808651328230 10<br />
						<!-- Oh good you read the source; here is e! Now plug it in and find the domain: 2.7182818284590452353602874713526624977572470936 8 -->
						</code>
						<em>This would be useful for solving the <a  target="_blank" href="/img.php?f=google.jpg&w=430">famous google billboard math problem</a>.</em>
						<br />
						Related link: <a href="//nkdv.co/1fl" target="_blank">2,000,000 digits of e</a><br />
						this page's shortlink: <a href="http://nkdv.co/<%INFO_PAGE_SHORTNAME%>" target="_blank">http://nkdv.co/<%INFO_PAGE_SHORTNAME%></a>
					</div>
				</span>
			</span>
		</div>
	</div>
	<%code-block%>

</div>
<script>
$(function () {
	$('#check').submit(function () {
		$.ajax( { data: { num: $('#num').val() }, url: '/works/coding/prime-number-check/', complete: function (XMLHttpRequest, textStatus) {

			$('#num').val(XMLHttpRequest.responseText);
			if(XMLHttpRequest.responseText == 'Enter a number ') {
				return false;
			}

		}});

		return false;
	});
});

</script>
