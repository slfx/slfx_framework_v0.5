<?php

$PAGE_ZONES['navsub'] = '<div class="col-xs-12 col-md-11 col-md-offset-1">
  <ul class="nav" id="nav-sub">
    <li>
      <ul id="coding_menu" class="drop_menu">
        <li class="open"><a id="coding_btn" class="protect drop_button nav_sub" href="#" onclick="return false">Coding</a>
          <ul class="sublist" id="coding_drop">
            <li><a href="/works/coding/prime-number-check" id="prime-number-check_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">Prime Number Check</a></li>

            <li><a href="/works/coding/url-shortener" id="url-shortener_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">URL Shortener</a></li>
            <li><a href="/works/coding/mysql-backup" id="mysql-backup_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">MySQL Backup</a></li>
            <li><a href="/works/coding/zipcode-distance" id="zipcode-distance_btn" class="nav_sub clickable add_active" onclick="$().closeDropMenus();">Zipcode Distance</a></li>
          </ul>
        </li>
      </ul>
    </li>
    <li><a href="/works/photography" id="photo_btn" class="nav_sub clickable add_active">Photography</a></li>
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
