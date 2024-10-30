<?php
/*
Plugin Name: Login External Redirect
Plugin URI:  http://unni.in/wp-plugin-redirect-non-user-to-another-url
Description: This plugin can redirect non users or not signed in users to any external or internal url.
Version:     1.0
Author:      Unnikrishnan S
Author URI:  http://unni.in
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Login External Redidect is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Login External Redidect is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Login External Redidect. If not, see https://www.gnu.org/licenses/gpl-2.0.html
*/
defined( 'ABSPATH' ) or die( 'No Messing!!' );
/**
Main Function
*/
function external_redirect(){
	/* Check if redirection is good */
	if($redirect_path = external_redirection_check()){
		/* Redirect teporarly 302, if perminant 301 */
		$previousUrl = $_SERVER['HTTP_REFERER'];
		if(get_option("external_redirect_path")){
			wp_redirect( get_option("external_redirect_url").$redirect_path, get_option("external_redirect_method") ); exit;
		} else {
			wp_redirect( get_option("external_redirect_url"), get_option("external_redirect_method") ); exit;
		}
	}
}

/**
	Checks if redirection is good
*/
function external_redirection_check(){
	$current_url = $_SERVER[HTTP_HOST].$_SERVER["REQUEST_URI"];
	$current_url = str_replace("http://","",$current_url);
	$current_url = str_replace("https://","",$current_url);
	
	$siteurl     = str_replace("http://","",get_option("siteurl"));
	$siteurl     = str_replace("https://","",$siteurl);
		
	$redirection_path = str_replace($siteurl,"",$current_url);

	/* Change "?" to / from the url */
	$current_url = str_replace("?","/",$_SERVER["REQUEST_URI"]);
	/* Explode url with / which makes easy to check if its wp-admin.php  */
	$current_url = explode("/",$_SERVER["REQUEST_URI"]);
	/* Set wp-admin as admin url */
	$admin_url	 = "wp-login.php";
	/* Checks if the url and external redirect are set */
	if(get_option("external_redirect") && get_option("external_redirect_url")){
		/* Checks if admin url is current url */
  		if(!in_array($admin_url, $current_url)){
  			/* Check if user is signed in */
			if ( !is_user_logged_in() ) {
				/* Good to redirect */
	      		return $redirection_path;
	      	}
	    } else {
	    	/* No need to redirect */
	    	return false;
	    }
	} else {
		/* Do not redirect */
		return false;
	}
	
}

/**
	Page for admin to set url redirection
*/
function external_redirect_settings_page(){	
	if ( !current_user_can( 'install_plugins' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	if(isset($_POST['submit'])){
		update_option("external_redirect_url", $_POST['external_redirect_url']);
		if(isset($_POST['external_redirect'])){
			update_option("external_redirect", true);	
		} else {
			update_option("external_redirect", false);
		}
		if(isset($_POST['external_redirect_method'])){
			update_option("external_redirect_method", '301');
		} else {
			update_option("external_redirect_method", '302');
		}
		if(isset($_POST['external_redirect_path'])){
			update_option("external_redirect_path", '1');
		} else {
			update_option("external_redirect_path", '0');
		}
	}
	echo '<div class="wrap">';
	echo '<p><h1>Login External Redirect</h1></p>';
	echo '</div>';

	echo '<form method="POST">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="blogname">Site Title</label></th>
						<td>
							<input name="external_redirect_url" type="text" id="external_redirect_url" value="';
							if(get_option("external_redirect_url")){ 
								echo get_option("external_redirect_url");
							} else {
								echo 'http://unni.in/wp-plugin-redirect-non-user-to-another-url';
							}
							echo '" class="regular-text code">
						</td>
					</tr>
					<tr>
						<th scope="row">Redirection</th>
						<td>
							<fieldset><legend class="screen-reader-text"><span>Redirect</span></legend><label for="external_redirect">
								<input name="external_redirect" ';
								if(get_option("external_redirect")){ 
									echo 'checked = "checked"' ;
								}
								echo' type="checkbox" id="external_redirect" value="1"> Enable Redirection</label>
							</fieldset>
						</td>
					</tr>
<tr>
						<th scope="row">Method</th>
						<td>
							<fieldset><legend class="screen-reader-text"><span>Method</span></legend><label for="external_redirect_method">
								<input name="external_redirect_method" ';
								if(get_option("external_redirect_method")==301){ 
									echo 'checked = "checked"' ;
								}
								echo' type="checkbox" id="external_redirect_method" value="301">Enable permanent redirect (301)</label>
							</fieldset>
						</td>
					</tr>
<tr>
						<th scope="row">Path Redirect</th>
						<td>
							<fieldset><legend class="screen-reader-text"><span>Path Redirect</span></legend><label for="external_redirect_path">
								<input name="external_redirect_path" ';
								if(get_option("external_redirect_path")){ 
									echo 'checked = "checked"' ;
								}
								echo' type="checkbox" id="external_redirect_path" value="1"> Enable path redirection</label>
							</fieldset>
						</td>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
			</p>
		</form>';
}

/**
	settings function for admin 
*/
function external_redirect_settings() {
	add_options_page('Login External Redirect', 'Redirect', 'install_plugins', 'login-external-redirect', 'external_redirect_settings_page');
}

/**
	Runs redirect function when plugin is loaded
*/
add_action( 'plugins_loaded', 'external_redirect' );

/**
	Adds Admin Menu
*/
add_action('admin_menu', 'external_redirect_settings');
?>