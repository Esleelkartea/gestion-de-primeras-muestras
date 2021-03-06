<?php /* ADMIN $Id: viewuser.php,v 1.4 2006/03/27 18:08:23 Attest sw-libre@attest.es  Exp $ */
/* ADMIN $Id: viewuser.php,v 1.39 2005/04/08 06:52:59 ajdonnison Exp $ */

$user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : 0;

if ($user_id != $AppUI->user_id && ( ! $perms->checkModuleItem('admin', 'view', $user_id) || ! $perms->checkModuleItem('users', 'view', $user_id) ) )
	$AppUI->redirect('m=public&a=access_denied');

$AppUI->savePlace();

if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'UserVwTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'UserVwTab' ) !== NULL ? $AppUI->getState( 'UserVwTab' ) : 0;

// pull data
$q  = new DBQuery;
$q->addTable('users', 'u');
$q->addQuery('u.*');
$q->addQuery('con.*, company_id, company_name, dept_name, dept_id');
$q->addJoin('contacts', 'con', 'user_contact = contact_id');
$q->addJoin('companies', 'com', 'contact_company = company_id');
$q->addJoin('departments', 'dep', 'dept_id = contact_department');
$q->addWhere('u.user_id = '.$user_id);
$sql = $q->prepare();
$q->clear();

if (!db_loadHash( $sql, $user )) {
	$titleBlock = new CTitleBlock( 'ID usuario inv�lido', 'helix-setup-user.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=admin", "users list" );
	$titleBlock->show();
} else {

// setup the title block
	$titleBlock = new CTitleBlock( 'View User', 'helix-setup-user.png', $m, "$m.$a" );
	/*ina*/
	if ($canEdit || $user_id == $AppUI->user_id) {
	      $titleBlock->addCrumb( "?m=admin&a=addedituser&user_id=$user_id", "edit this user" );
	      //$titleBlock->addCrumb( "?m=system&a=addeditpref&user_id=$user_id", "edit preferences" );
	      $titleBlock->addCrumbRight(
			'<a href="#" onclick="popChgPwd();return false">' . $AppUI->_('change password') . '</a>'
	      );
	      if ($canAdd) $titleBlock->addCell('<td align="right" width="100%"><input type="button" class=button value="'.$AppUI->_('add user').'" onClick="javascript:window.location=\'./index.php?m=admin&a=addedituser\';" /></td>');
	}
	$titleBlock->show();
?>
<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit || $user_id == $AppUI->user_id) {
?>
function popChgPwd() {
	window.open( './index.php?m=public&a=chpwd&dialog=1&user_id=<?php echo $user['user_id']; ?>', 'chpwd', 'top=250,left=250,width=350, height=220, scollbars=false' );
}
<?php } ?>
</script>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr valign="top">
	<td width="50%">
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Login Name');?>:</td>
			<td class="hilite" width="100%"><?php echo $user["user_username"];?></td>
		</tr>
		
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Real Name');?>:</td>
			<td class="hilite" width="100%"><?php echo $user["contact_first_name"].' '.$user["contact_last_name"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Company');?>:</td>
			<td class="hilite" width="100%">
				<a href="?m=companies&a=view&company_id=<?php echo @$user["contact_company"];?>"><?php echo @$user["company_name"];?></a>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Department');?>:</td>
			<td class="hilite" width="100%">
				<a href="?m=departments&a=view&dept_id=<?php echo @$user["contact_department"];?>"><?php echo $user["dept_name"];?></a>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Phone');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_phone"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Home Phone');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_phone2"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Mobile');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_mobile"];?></td>
		</tr>
		
		</table>

	</td>
	<td width="50%">
		<table width="100%">
		<!--ina-->
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Email');?>:</td>
			<td class="hilite" width="100%"><?php echo '<a href="mailto:'.@$user["contact_email"].'">'.@$user["contact_email"].'</a>';?></td>
		</tr>
		
		<tr valign=top>
			<td align="right" nowrap><?php echo $AppUI->_('Address');?>:</td>
			<td class="hilite" width="100%"><?php
				echo @$user["contact_address1"]
					.( ($user["contact_address2"]) ? '<br />'.$user["contact_address2"] : '' );
			?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('etiq_Ciudad');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_city"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('etiq_Provincia');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_state"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Postcode');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_zip"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('etiq_Pais');?>:</td>
			<td class="hilite" width="100%"><?php echo @$user["contact_country"];?></td>
		</tr>
		<!--ina-->
		</table>
	</td>
</tr>
</table>
<!--ina-->
<?php
	// tabbed information boxes
	$tabBox = new CTabBox( "?m=admin&a=viewuser&user_id=$user_id", "{$dPconfig['root_dir']}/modules/admin/", $tab );
	
	//ina 

	$tabBox->add( 'vw_usr_roles', 'Roles' );
	$tabBox->show();
}
?>
