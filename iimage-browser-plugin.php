<?php

// ====( configuration options )================================================
$ib_use_quicktags_button = true; //if quicktags button doesn't work for you
								//change this to 'false'
// ====( STOP EDITING HERE )====================================================

/*
Plugin Name: IImage Browser
Version: 1.5.2
Plugin URI: http://fredfred.net/skriker/index.php/iimage-browser
Description: This plugin adds an "IImage Browser" button to the Quicktags area which opens an image browser to select from all previously uploaded images and add the appropriate code to the post.
Author: Martin Chlupáč
Author URI: http://fredfred.net/skriker/
Update: http://fredfred.net/skriker/plugin-update.php?p=85
*/ 

/*
IImage Browser Plugin for Wordpress 1.2 and higher
Copyright (C) 2004-2005 Martin Chlupac

based on

Image Browser Plugin for Wordpress 1.2
Copyright (C) 2004 Florian Jung

also using

Edit Button Template
Owen Winkler, http://www.asymptomatic.net/wp-hacks

This program is free software; you can redistribute it and/or 
modify it under the terms of the GNU General Public License as 
published by the Free Software Foundation; either version 2 of the 
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but 
WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
General Public License for more details.

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 
USA
*/



if($ib_use_quicktags_button){
			
			add_filter('admin_footer', 'callback_iimagebrowser');
			
			function callback_iimagebrowser()
			{
				if(strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'comment.php') || strpos($_SERVER['REQUEST_URI'], 'page.php') || strpos($_SERVER['REQUEST_URI'], 'post-new.php') || strpos($_SERVER['REQUEST_URI'], 'page-new.php') || strpos($_SERVER['REQUEST_URI'], 'bookmarklet.php'))
				{
			?>
			<script language="JavaScript" type="text/javascript"><!--
			var toolbar = document.getElementById("ed_toolbar");
			<?php
					edit_insert_button("IImage Browser", "iimagebrowser", "IImage Browser");
			?>
			
			function iimagebrowser()
			{
			
			window.open("../wp-admin/iimage-browser.php", "IImageBrowser", "width=700,height=600,scrollbars=yes");
			
			}
			
			//--></script>
			
			
			<?php
				}
			}
			
			if(!function_exists('edit_insert_button'))
			{
				//edit_insert_button: Inserts a button into the editor
				function edit_insert_button($caption, $js_onclick, $title = '')
				{
				?>
				if(toolbar)
				{
					var theButton = document.createElement('input');
					theButton.type = 'button';
					theButton.value = '<?php echo $caption; ?>';
					theButton.onclick = <?php echo $js_onclick; ?>;
					theButton.className = 'ed_button';
					theButton.title = "<?php echo $title; ?>";
					theButton.id = "<?php echo "ed_{$caption}"; ?>";
					toolbar.appendChild(theButton);
				}
				<?php
			
				}
			}

}//end of $ib_use_quicktags_button
else {
		// Create a link to the image selector in the post screen
		function ib_print_insert_image_link() {
			
			if(strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'page-new.php') || strpos($_SERVER['REQUEST_URI'], 'bookmarklet.php')){
			
				print '<div id="insertimage">'
					. '<a href="iimage-browser.php"'
					. ' onclick="return insert_image_popup(this, \'IImageBrowser\')">'
					. 'Insert Image</a></div>';
			}
					
			
		}
		
		// The popup needs some CSS and javascript in the head of the page
		function ib_print_insert_image_head() {
			print '
			<style type="text/css">
			#insertimage {
				position: absolute;
				top: 4px;
				right: 4px;
				margin: 0; padding: 0;
				font-size: 1em;
			}
			</style>
			<script type="text/javascript">
			<!--
			function insert_image_popup(mylink, windowname) {
				if (! window.focus) return true;
				var href;
				if (typeof(mylink) == "string")
					href = mylink;
				else
					href = mylink.href;
				window.open(href, windowname, "width=600,height=600,scrollbars=yes");
				return false;
			}
			//-->
			</script>
			';
		}
		
		// Add actions to call the function
		add_action('admin_head',   'ib_print_insert_image_head');
		add_action('admin_footer', 'ib_print_insert_image_link');
		

}
?>