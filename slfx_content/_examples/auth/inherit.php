<?php

$PAGE_ZONES['navsub'] = '<div class="col-xs-12 col-md-11 col-md-offset-1">
  <ul class="nav" id="nav-sub">
    <li><span> Auth Menu </span></li>

    <li>
      <ul id="auth_menu" class="drop_menu">
        <li class="open"><a id="auth_btn" class="protect drop_button nav_sub" href="#" onclick="return false">Auth Functions</a>
          <ul class="sublist" id="coding_drop">
            <li><a href="/_examples/auth/list" id="list_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">List</a></li>
            <li><a href="/_examples/auth/create" id="create_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">Create</a></li>
            <li><a href="/_examples/auth/signin" id="signin_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">Log In</a></li>
          </ul>
        </li>
      </ul>
    </li>

  </ul>
</div>';



/* used for code coloring */
$find_arry = array(
	'style="color: #000000"',
	'style="color: #007700"',
	'style="color: #0000BB"',
	'style="color: #FF8000"',
	'style="color: #DD0000"',
	'style="color: #000000"',
	'style="color: #000000"'
);


$replace_arry = array(
	'class="code-container"',
	'class="color-reserved"',
	'class="color-special"',
	'class="color-comment"',
	'class="color-string"'
);

 ?>
