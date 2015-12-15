<?php /* CONTACTS $Id: addedit.php,v 1.35 2006/04/07 11:47:45 Attest sw-libre@attest.es  Exp $ */
/* CONTACTS $Id: addedit.php,v 1.34 2005/03/03 23:13:47 jcgonz Exp $ */
$contact_id = intval( dPgetParam( $_GET, 'contact_id', 0 ) );
$company_id = intval( dPgetParam( $_REQUEST, 'company_id', 0 ) );
$company_name = dPgetParam( $_REQUEST, 'company_name', null );

// check permissions for this record
$perms =& $AppUI->acl();
if (! ($canEdit = $perms->checkModuleItem( 'contacts', 'edit', $contact_id )) ) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// load the record data
$msg = '';
$row = new CContact();
$canDelete = $row->canDelete( $msg, $contact_id );

if (!$row->load( $contact_id ) && $contact_id > 0) {
	$AppUI->setMsg( 'Contact' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
} else if ($row->contact_private && $row->contact_owner != $AppUI->user_id
	&& $row->contact_owner && $contact_id != 0) {
// check only owner can edit
	$AppUI->redirect( "m=public&a=access_denied" );
}

// setup the title block
$ttl = $contact_id > 0 ? "Edit Contact" : "Add Contact";
$titleBlock = new CTitleBlock( $ttl, 'monkeychat-48.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=contacts", "contacts list" );
if ($canEdit && $contact_id) {
	$titleBlock->addCrumbDelete( 'delete contact', $canDelete, $msg );
}
$titleBlock->show();
$company_detail = $row->getCompanyDetails();
$dept_detail = $row->getDepartmentDetails();
if ($contact_id == 0 && $company_id > 0) {
	$company_detail['company_id'] = $company_id;
	$company_detail['company_name'] = $company_name;
	
}

?>

<script language="javascript">
<?php
	echo "window.company_id=" . dPgetParam($company_detail, 'company_id', 0) . ";\n";
	echo "window.company_value='" . dPgetParam($company_detail, 'company_name', "") . "';\n";
?>

function submitIt() {
	var form = document.changecontact;
	if (form.contact_last_name.value.length < 1) {
		alert( "<?php echo $AppUI->_('contactsValidName', UI_OUTPUT_JS);?>" );
		form.contact_last_name.focus();
	} else if (form.contact_first_name.value.length < 1) {
		alert( "<?php echo $AppUI->_('contactsOrderBy', UI_OUTPUT_JS);?>" );
		form.contact_first_name.focus();
	}
	else {
		if (form.contact_department_name.value =='')   form.contact_department.value = ''; 
		form.contact_order_by.value= form.contact_last_name.value +', '+form.contact_first_name.value;
		form.submit();
	} 
}

function popDepartment() {
//        window.open('./index.php?m=public&a=selector&dialog=1&callback=setDepartment&table=departments&hide_company=1&company_id=' + window.company_id, 'department','left=50,top=50,height=250,width=400,resizable');
	window.open("./index.php?m=contacts&a=select_contact_company&dialog=1&table_name=departments&company_id="+window.company_id, "company", "scroll=yes,left=50,top=50,height=290,width=400,resizable");
}

function setDepartment( key, val ){
	var f = document.changecontact;
 	if (val != '') {
    	f.contact_department.value = key;
			f.contact_department_name.value = val;
    }
}

function popCompany() {
//  window.open('./index.php?m=public&a=selector&dialog=1&callback=setCompany&table=companies', 'company','left=50,top=50,height=250,width=400,resizable');
	window.open("./index.php?m=contacts&a=select_contact_company&dialog=1&table_name=companies&company_id=<?php echo $company_detail['company_id'];?>", "company", "left=50,top=50,height=290,width=400,resizable");
}

function setCompany( key, val ){
	var f = document.changecontact;
 	if (val != '') {
    	f.contact_company.value = key;
    	f.contact_company_name.value = val;
    	if ( window.company_id != key ){
    		f.contact_department.value = "";
				f.contact_department_name.value = "";
    	}
    	window.company_id = key;
    	window.company_value = val;
    }
}

function delIt() {
	var form = document.changecontact;
	if(confirm( "<?php echo $AppUI->_('contactsDelete', UI_OUTPUT_JS);?>" )) {
		form.del.value = "<?php echo $contact_id;?>";
		form.submit();
	}
}

function orderByName( x ) {
	var form = document.changecontact;
	if (x == "name") {
		form.contact_order_by.value = form.contact_last_name.value + ", " + form.contact_first_name.value;
	} else {
		form.contact_order_by.value = form.contact_company_name.value;
	}
}

function companyChange() {
	var f = document.changecontact;
	if ( f.contact_company.value != window.company_value ){
		f.contact_department.value = "";
	} 
}

</script>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">

<form name="changecontact" action="?m=contacts" method="post">
	<input type="hidden" name="dosql" value="do_contact_aed" />
	<input type=hidden name=cont value=''>
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="contact_project" value="0" />
	<input type="hidden" name="contact_unique_update" value="<?php echo uniqid("");?>" />
	<input type="hidden" name="contact_id" value="<?php echo $contact_id;?>" />
	<input type="hidden" name="contact_owner" value="<?php echo $row->contact_owner ? $row->contact_owner : $AppUI->user_id;?>" />

<tr>
	<td colspan="2">
		<table border="0" cellpadding="1" cellspacing="1">
		<tr>
			<td align="right"><?php echo $AppUI->_('First Name');?>:</td>
			<td>
				<input type="text" class="text" size=25 name="contact_first_name" value="<?php echo @$row->contact_first_name;?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Last Name');?>:</td>
			<td>
				<input type="text" class="text" size=25 name="contact_last_name" value="<?php echo @$row->contact_last_name;?>" maxlength="50" <?php if($contact_id==0){?> onBlur="orderByName('name')"<?php }?> />
				<!--<a href="#" onClick="orderByName('name')">[<?php echo $AppUI->_('use in display');?>]</a>-->
			</td>
		</tr>
		
		<input type="hidden" name="contact_order_by" value="<?php echo @$row->contact_order_by;?>">
		<!--<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Private Entry');?>: </td>
			<td>
				<input type="checkbox" value="1" name="contact_private" <?php echo (@$row->contact_private ? 'checked' : '');?> />
			</td>
		</tr>-->
		</table>
	</td>
</tr>
	<td valign="top" width="50%"><br><br>
		<table border="0" cellpadding="1" cellspacing="1" class="details" width="100%">
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('etiq_Cargo');?>:</td>
			<td nowrap>
				<input type="text" class="text" name="contact_job" value="<?php echo @$row->contact_job;?>" maxlength="100" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Company');?>:</td>
			<td nowrap>
				<input type="text" disabled class="text" name="contact_company_name" value="<?php 
					echo $company_detail['company_name'];
					?>" maxlength="100" size="25" />&nbsp;
				<input type="button" class="button" value="<?php echo $AppUI->_('select company...');?>" onclick="popCompany()" />
				<input type='hidden' name='contact_company' value="<?php echo $company_detail['company_id']; ?>">
				<!--<a href="#" onClick="orderByName('company')">[<?php echo $AppUI->_('use in display');?>]</a>-->
				</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Department');?>:</td>
			<td nowrap>
				<input type="text" class="text" name="contact_department_name" disabled value="<?php echo $dept_detail['dept_name'];?>" maxlength="100" size="25" />

				<input type='hidden' name='contact_department' value='<?php echo $dept_detail['dept_id'];?>'>
				<input type="button" class="button" value="<?php echo $AppUI->_('select department...');?>" onclick="popDepartment()" />
				<?if ($dept_detail['dept_id']!='') echo "<script>document.changecontact.cont.value = 1;</script>";
				else echo "<script>document.changecontact.cont.value = 0;</script>";?>
				</td>
		</tr>
		
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Address');?>:</td>
			<td><input type="text" class="text" name="contact_address1" value="<?php echo @$row->contact_address1;?>" maxlength="60" size="25" /></td>
		</tr>
		
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('City');?>:</td>
			<td><input type="text" class="text" name="contact_city" value="<?php echo @$row->contact_city;?>" maxlength="30" size="25" /></td>
		</tr>
		
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('etiq_Provincia');?>:</td>
			<td><input type="text" class="text" name="contact_state" value="<?php echo @$row->contact_state;?>" maxlength="30" size="25" /></td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Postcode');?>:</td>
			<td><input type="text" class="text" name="contact_zip" value="<?php echo @$row->contact_zip;?>" maxlength="11" size="25" /></td>
		</tr>
	
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Phone');?>:</td>
			<td>
				<input type="text" class="text" name="contact_phone" value="<?php echo @$row->contact_phone;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Phone');?>2:</td>
			<td>
				<input type="text" class="text" name="contact_phone2" value="<?php echo @$row->contact_phone2;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Mobile Phone');?>:</td>
			<td>
				<input type="text" class="text" name="contact_mobile" value="<?php echo @$row->contact_mobile;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<!--
		
		-->
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Fax');?>:</td>
			<td>
				<input type="text" class="text" name="contact_fax" value="<?php echo @$row->contact_fax;?>" maxlength="30" size="25" />
			</td>
		</tr>
	
		
		
		
		<tr>
			<td align="right"><?php echo $AppUI->_('Email');?>:</td>
			<td>
				<input type="text" class="text" name="contact_email" value="<?php echo @$row->contact_email;?>" maxlength="255" size="25" />
			</td>
		</tr>
		
		</table>
	</td>
	<!--<td valign="top" width="50%">
		<strong><?php echo $AppUI->_('Contact Notes');?></strong><br />
		<textarea class="textarea" name="contact_notes" rows="20" cols="40"><?php echo @$row->contact_notes;?></textarea></td>
	</td>-->
</tr>
<tr>
	<td>
		<!--<input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:window.location='./index.php?m=contacts';" />-->
		<input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:history.back();" />
	</td>
	<td align="right">
		<input type="button" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitIt()" />
	</td>
</tr>

</form>
</table>
<script>
//alert (document.changecontact
</script>