<div class="container-fluid">
	<div class="row parallax parallax-1" style="background-image: <%parallax-image%>">
		<div class="col-xs-12">
			<h1 class="top">Auth Example::Create</h1>
			<p class="top">This example auth::create user method.</p>
		</div>		
	</div>
	<div class="row parallax-2-backdrop" style="background-image: <%parallax-image%>">
	<div class="parallax parallax-2"  style="background-image: <%parallax-image%>"></div>
	</div>
	<div class="row" style="background: #eaeaea;">
		<%navsub%>
	</div>
</div>
<div class="container-fluid white-bg">
	<div class="row" style=" padding-top: 60px;padding-bottom: 60px ">
		<div class="col-xs-12 col-md-7 col-md-offset-3">
			<div class="container-fluid">
				<div class="row">			
					<div class="col-xs-12" id="signup_register">
						<form class="validateForm" id="register" name="valid" method="post" action="<%ajax_api%>">
							<div class="error"> <%form_error%> </div>
							<fieldset>
								<div class="container-fluid">
									<div class="row">
										<div class="col-xs-12 col-md-7 padding-10">
											<p>
												<label for="create_email">E-mail</label>
												<input id="create_email" name="create_email" type="text"  class="email required" value="<%create_email%>" />
												
											</p>
										
										</div>
										<div class="col-xs-12 col-md-5 padding-10 text-left-xs email element_response">
											<div class="error_feedback"> </div>
										
										</div>
										
									</div>
										<div class="row">
										<div class="col-xs-12 col-md-7 padding-10">
											<p>
												<label for="create_password">Password</label>
												<input id="create_password" name="create_password" type="password" class="password required" value="" />
											</p>
											
										</div>
										<div class="col-xs-12 col-md-5 padding-10 text-left-xs password element_response">
											<div class="error_feedback"> </div>
											<input type="checkbox" name="change_text" onchange="document.getElementById('create_password').type = this.checked ? 'text' : 'password'"> Show password
										
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-md-7">
											<input type="hidden" name="origin" value="register" />
											<p class="graybox">
												<span>
													<input type="submit" name="register" value="Create my account" class="btn" alt="Create my account" />
												</span>
											</p>
										</div>
										<div class="col-xs-12 col-md-5 text-center-xs text-left-md">
											<div class="tos"><br />
												Printable versions<br />
												<a href="/privacy" class="protect" target="_blank">Privacy Policy</a> - <a href="/tos"  class="protect" target="_blank">Terms of Service</a>
											</div>
										</div>
									</div>
								</div>
			
							</fieldset>
			
						</form>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>



















