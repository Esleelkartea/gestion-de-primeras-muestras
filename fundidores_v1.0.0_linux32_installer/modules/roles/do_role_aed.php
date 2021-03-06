<?php /* ROLES $Id: do_role_aed.php,v 1.3 2004/02/14 18:49:38 Attest sw-libre@attest.es Exp $ */
/* ROLES $Id: do_role_aed.php,v 1.2 2004/10/15 01:32:55 ajdonnison Exp $ */
$del = isset($_POST['del']) ? $_POST['del'] : 0;

$role =& new CRole();

if (($msg = $role->bind( $_POST ))) {
	$AppUI->setMsg( $msg, UI_MSG_ERROR );
	$AppUI->redirect();
}

if ($del) {
	if (($msg = $role->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "Role deleted", UI_MSG_ALERT );
	}
} else {
	if (($msg = $role->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['role_id'];
		$AppUI->setMsg( "Role ".($isNotNew ? 'updated' : 'inserted'), UI_MSG_OK );
	}
}
$AppUI->redirect( "m=roles" );
?>
