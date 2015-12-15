<?php /* STYLE/DEFAULT $Id: header.php,v 1.39 2006/04/05 18:40:00 Attest sw-libre@attest.es Exp $ */
/* STYLE/DEFAULT $Id: header.php,v 1.38 2005/04/01 09:13:00 gregorerhardt Exp $ */
$dialog = dPgetParam( $_GET, 'dialog', 0 );
if ($dialog)
	$page_title = '';
else
	$page_title = ($dPconfig['page_title'] == 'dotProject') ? $dPconfig['page_title'] . '&nbsp;' . $AppUI->getVersion() : $dPconfig['page_title'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="Description" content="ATTEST Default Style" />
	<meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
	<title><?php echo @dPgetConfig( 'page_title' );?></title>
	<link rel="stylesheet" type="text/css" href="./style/<?php echo $uistyle;?>/main.css" media="all" />
	<style type="text/css" media="all">@import "./style/<?php echo $uistyle;?>/main.css";</style>
	<link rel="shortcut icon" href="./style/<?php echo $uistyle;?>/images/favicon.ico" type="image/ico" />
	<?php @$AppUI->loadJS(); ?>
	
	
</head>
<body onload="this.focus();<?if ($_GET["impr"]==1) {?> window.print();window.location='?m=<?=$_GET['m']?>';<?}?>">
<?if ($_GET["impr"]!=1){?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td valign="center"><table width='100%' cellpadding=3 cellspacing=0 border=0><tr>
	<th class="banner" ><img src='./images/miniAFV.gif' width=40 heigth=40 ></th>
	<th  class="banner" align="left" valign="center"> <strong><?php
		echo "<h1><a style='color: white' href='{$dPconfig['base_url']}'>$page_title</a><h1>";
	?></strong></th>
	
	</tr></table></td>
</tr>
<?php if (!$dialog) {
	// top navigation menu
	$nav = $AppUI->getMenuModules();
	$perms =& $AppUI->acl();
?>
<tr>
	<td class="nav" align="left">
	<table width="100%" cellpadding="3" cellspacing="0" width="100%">
	<tr>
		<td height="20">
		<?php
		$links = array();
		foreach ($nav as $module) {
			if ($perms->checkModule($module['mod_directory'], 'access') && ($module['mod_directory']!='help')) {
				$links[] = '<a href="?m='.$module['mod_directory'].'">'.$AppUI->_($module['mod_ui_name']).'</a>';
			}
		}
		$links[] = '<a href="manual_usuario.pdf" target="_black">'.$AppUI->_('Help').'</a>'; //la ayuda va aparte
		echo implode( ' | ', $links );
		echo "\n";
		?>
		</td>
<!--ina-->

		<td nowrap="nowrap" align="right">
				
				<a href="./index.php?m=admin&a=viewuser&user_id=<?php echo $AppUI->user_id;?>"><?php echo $AppUI->_('My Info');?></a> |
<?php
	if ($perms->checkModule('calendar', 'access')) {
		$now = new CDate();
?>                             <!--ina-->
		
		
		<?php } ?>
				<a href="./index.php?logout=-1"><?php echo $AppUI->_('Logout');?></a>&nbsp;&nbsp;&nbsp;
			</td>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
		<table cellspacing="0" cellpadding="3" border="0" width="100%">
		<tr>
			<td width="100%"><?php echo $AppUI->_('Welcome')." $AppUI->user_first_name $AppUI->user_last_name"; ?></td>
			
		</tr>
		</table>
	</td>
</tr>
<?php } // END showMenu ?>
</table>

<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
<td valign="top" align="left" width="98%">
<?php
	echo $AppUI->getMsg();
}?>