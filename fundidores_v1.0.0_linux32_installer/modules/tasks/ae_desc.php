<?php // $Id: ae_desc.php,v 1.92 2006/04/05 15:59:23 Attest sw-libre@attest.es Exp $
// $Id: ae_desc.php,v 1.9 2005/04/07 00:11:07 jcgonz Exp $

	global $AppUI, $task_id, $obj, $users, $task_access, $department_selection_list;
	global $task_parent_options, $dPconfig, $projects, $task_project, $can_edit_time_information, $tab;

	$perms =& $AppUI->acl();
?>
<form action="?m=tasks&a=addedit&task_project=<?php echo $task_project; ?>"
  method="post"  name="detailFrm">
<input type="hidden" name="dosql" value="do_task_aed" />
<input type="hidden" name="sub_form" value="1" />
<input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
<table class="std" width="100%" border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width="50%" valign='top'>
	    <table border="0">
	    	<tr>
	    		<td>
				    			<?php
				    				if($can_edit_time_information){
				    					?>
								<?php echo $AppUI->_( 'Task Owner' );?>
								<br />
							<?php echo arraySelect( $users, 'task_owner', 'class="text"', !isset($obj->task_owner) ? $AppUI->user_id : $obj->task_owner );?>
								<br />
									<?php
				    				} // $can_edit_time_information
								?>
								
								
							</td>
							
				</td>
			</tr>
		<tr>
			<td><?php echo $AppUI->_( 'Task Parent' );?>:</td>
			<!--<td><?php echo $AppUI->_( 'Target Budget' );?>:</td> -->
		</tr>
		<tr>
			<td>
				<select name='task_parent' class='text'>
					<option value='<?php echo $obj->task_id; ?>'><?php echo $AppUI->_('None'); ?></option>
					<?php echo $task_parent_options; ?>
				</select>
			</td>
		<!--ina	<td><?php echo $dPconfig['currency_symbol'] ?><input type="text" class="text" name="task_target_budget" value="<?php echo @$obj->task_target_budget;?>" size="10" maxlength="10" /></td> -->
		</tr>
	<!--ina mover tareas a proyecto --><?
	if ($AppUI->isActiveModule('contacts') && $perms->checkModule('contacts', 'view')) {
							echo "<input type='button' class='button' value='".$AppUI->_("Select contacts...")."' onclick='javascript:popContacts();' />";
						}?>
		</td></tr>
		</table>
	</td>
	<td valign="top" align="center">
		<table><tr><td align="left">
		<?php echo $AppUI->_( 'Description' );?>:
		<br />
		<textarea name="task_description" class="textarea" cols="60" rows="10" wrap="virtual"><?php echo @$obj->task_description;?></textarea>
		</td></tr></table><br />
		<?php
			require_once("./classes/CustomFields.class.php");
			GLOBAL $m;
			$custom_fields = New CustomFields( $m, 'addedit', $obj->task_id, "edit" );
			$custom_fields->printHTML();
		?>
	</td>
</tr>
</table>
</form>
<script language="javascript">
 subForm.push(new FormDefinition(<?php echo $tab;?>, document.detailFrm, checkDetail, saveDetail));
</script>
