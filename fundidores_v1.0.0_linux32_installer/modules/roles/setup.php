<?php
/*
 * Name:      Roles
 * Directory: roles
 * Version:   1.0.0
 * Type:      user
 * UI Name:   
 * UI Icon:
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Roles';		// name the module
$config['mod_version'] = '1.0.0';		// add a version number
$config['mod_directory'] = 'roles';		// tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetupRoles';	// the name of the PHP setup class (used below)
$config['mod_type'] = 'user';			// 'core' for modules distributed with dP by standard, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Roles';		// the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'Einstein.jpg';	// name of a related icon
$config['mod_description'] = '';	// some description of the module
$config['mod_config'] = true;			// show 'configure' link in viewmods

// show module configuration with the dPframework (if requested via http)
if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupRoles {

	
	function remove() {		// run this method on uninstall process
			// remove table from database

		return null;
	}


	

	function install() {
								// execute the queryString

		return null;
	}

}

?>