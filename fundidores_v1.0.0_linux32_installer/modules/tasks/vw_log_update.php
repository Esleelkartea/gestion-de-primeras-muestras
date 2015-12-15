<?php /* TASKS $Id: vw_log_update.php,v 1.31 2006/04/11 17:25:20 Attest sw-libre@attest.es Exp $ */
/* TASKS $Id: vw_log_update.php,v 1.30 2005/04/08 05:39:20 ajdonnison Exp $ */
GLOBAL $AppUI, $task_id, $obj, $percent, $can_edit_time_information;

$perms =& $AppUI->acl();

// check permissions
/*ina

*/
$canEdit = $perms->checkModule( 'task_log', 'edit' );
$canAdd = $perms->checkModule ( 'task_log', 'add' );

$task_log_id = intval( dPgetParam( $_GET, 'task_log_id', 0 ) );
$log = new CTaskLog();
if ($task_log_id) {
	if (! $canEdit)
		$AppUI->redirect("m=public&a=access_denied");
	$log->load( $task_log_id );
} else {
	if (! $canAdd)
		$AppUI->redirect("m=public&a=access_denied");
	$log->task_log_task = $task_id;
	$log->task_log_name = $obj->task_name;
}

// Check that the user is at least assigned to a task
$task = new CTask;
$task->load($task_id);
if (! $task->canAccess($AppUI->user_id))
	$AppUI->redirect('m=public&a=access_denied');

// Lets check which cost codes have been used before
/*ina
*/

$proj = &new CProject();
$proj->load($obj->task_project);
/*
ina
*/
$taskLogReference = dPgetSysVal( 'TaskLogReference' );

// Task Update Form
	$df = $AppUI->getPref( 'SHDATEFORMAT' );
	$log_date = new CDate( $log->task_log_date );

$sq="select max(task_log_date) from task_log where task_log_task=$task_id";
?>
<!-- TIMER RELATED SCRIPTS -->
<script language="JavaScript">
	// please keep these lines on when you copy the source
	// made by: Nicolas - http://www.javascript-page.com
	// adapted by: Juan Carlos Gonzalez jcgonz@users.sourceforge.net
	
	var ultimaFecha = parseInt(<?echo substr(str_replace('-','',db_LoadResult($sq)),0,8);?>);
	var fecha_max = <?$k = $task->task_start_date_ir;
					$k = substr($k,0,10);
					$k = str_replace('-','',$k);
					echo $k;?>;
					
	var timerID       = 0;
	var tStart        = null;
    var total_minutes = -1;
	
	function UpdateTimer() {
	   if(timerID) {
	      clearTimeout(timerID);
	      clockID  = 0;
	   }
	
       // One minute has passed
       total_minutes = total_minutes+1;
	   
	   document.getElementById("timerStatus").innerHTML = "( "+total_minutes+" <?php echo $AppUI->_('minutes elapsed'); ?> )";

	   // Lets round hours to two decimals
	   var total_hours   = Math.round( (total_minutes / 60) * 100) / 100;
	   document.editFrm.task_log_hours.value = total_hours;
	   
	   timerID = setTimeout("UpdateTimer()", 60000);
	}
	
	//ina function timerStart() 
		
	
	function timerStop() {
	   if(timerID) {
	      clearTimeout(timerID);
	      timerID  = 0;
          total_minutes = total_minutes-1;
	   }
	}
	
	function timerReset() {
		document.editFrm.task_log_hours.value = "0.00";
        total_minutes = -1;
	}

	function timerSet() {
		total_minutes = Math.round(document.editFrm.task_log_hours.value * 60) -1;
	}
	
	function cam (a) {
		var sep = ' :: ';
		var task = '<?echo $obj->task_name;?>';
		var obj = document.getElementById('task_log_name_text') ;
		if (a.indexOf(task+' ::')) {
			obj.value= task + sep + a;
		}
		
	}
	
	function cambio () {
		if (document.editFrm.task_percent_complete.value==100)	 {
			document.getElementById('email_dep_ch').checked=true;
			
			
		}
	}
	function porciento(f) {

		if(ultimaFecha > f) {
			window.document.editFrm.task_percent_complete.disabled = true;
			window.document.editFrm.task_percent_complete.title ='Existen acciones posteriores con este porcentaje';
		} else window.document.editFrm.task_percent_complete.disabled = false;
	}

	

</script>
<!-- END OF TIMER RELATED SCRIPTS -->

<a name="log"></a>
<form name="editFrm" action="?m=tasks&a=view&task_id=<?php echo $task_id;?>" method="post" onSubmit="javascript:updateEmailContacts();">
	<input type="hidden" name="uniqueid" value="<?php echo uniqid("");?>" />
	<input type="hidden" name="dosql" value="do_updatetask" />
	<input type="hidden" name="task_log_id" value="<?php echo $log->task_log_id;?>" />
	<input type="hidden" name="task_log_task" value="<?php echo $log->task_log_task;?>" />
	<input type="hidden" name="task_log_creator" value="<?php echo $AppUI->user_id;?>" />
	<input type="hidden" name="task_log_name" value="Update :<?php echo $log->task_log_name;?>" />
<table cellspacing="1" cellpadding="2" border="0" width="100%">
<tr>
    <td width='40%' valign='top' align='center'>
      <table width='100%' border=0>
<tr>
	<td align="right">
		<?php echo $AppUI->_('Date');?>
	</td>
	<td nowrap="nowrap">
	<!-- patch by rowan  bug #890841 against v1.0.2-1   email: bitter at sourceforge dot net -->
		<input type="hidden" name="task_log_date" value="<?php echo $log_date->format( FMT_DATETIME_MYSQL );?>">
	<!-- end patch #890841 -->
		<input type="text" name="log_date" value="<?php echo $log_date->format( $df );?>" class="text" disabled="disabled">
		<a href="#" onClick="popCalendar('log_date')">
			<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0" />
		</a>
	</td>
</tr>
<tr>
	<td align="right"><?php echo $AppUI->_('Progress');?></td>
	<td>
		<table>
		   <tr>
		      <td>
<?php

?>
<?
	echo arraySelect( $percent, 'task_percent_complete', 'id="task_percent_complete" size="1" class="text" onChange="javascript: cambio();"', $obj->task_percent_complete ) . '%';
?>
		      </td>
		      <td valign="middle" >
			<?php
				if ( $obj->task_owner != $AppUI->user_id ){
					echo "<input type='checkbox' name='task_log_notify_owner' /></td><td valign='middle'>" . $AppUI->_('Notify creator');	
				}
			?>		 	
		     </td>
		   </tr>
		</table>
	</td>

</tr>



<tr>
	<td align="right">
		<?php echo $AppUI->_('Hours Worked');?>
	</td>
	<td colspan=2 align=left >&nbsp;&nbsp;&nbsp;
		<input type="text" class="text" name="task_log_hours" value="<?php echo $log->task_log_hours;?>" maxlength="8" size="6" /> 
		<!--ina -->
		<span id='timerStatus'></span>
	</td>
</tr>
<!--ina-->
<?php
	if($obj->canUserEditTimeInformation()) {
?>
	

<tr>
		<!--ina-->

		
		<td>
			<script language='javascript'>
				function popCalendar( field ){
					calendarField = field;
					idate = eval( 'document.editFrm.task_' + field + '.value' );
					window.open( 'index.php?m=public&a=calendar&b=1&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=251, height=220, scollbars=false' );
				}
			</script>
			<?php
				$end_date = intval( $obj->task_end_date ) ? new CDate( $obj->task_end_date ) : null;
			?>
			<!--ina-->
		</td>
	</tr>
	<tr>
			<td align="right" valign="top"><?php echo $AppUI->_('Email Log to');?>:</td>
			
		<?php
			$tl = $AppUI->getPref('TASKLOGEMAIL');
			$ta = $tl & 1;
			$tt = $tl & 2;
			$tp = $tl & 4;
		?>
		<td align="left">
				&nbsp;&nbsp;&nbsp;<input type='checkbox' name='email_assignees' >
						<?php echo $AppUI->_('Task Assignees');?>
				</br>		
		
				<input type='hidden' name='email_task_list' id='email_task_list'
				  value='<?php
						$titulo1 = array();
						$q = new DBQuery;
						$q->addTable('task_contacts', 'tc');
						$q->leftJoin('contacts', 'c', 'c.contact_id = tc.contact_id');
						$q->addWhere("tc.task_id = '$task_id'");
						$q->addQuery('tc.contact_id');
						$q->addQuery('c.contact_first_name, c.contact_last_name');
						$req =& $q->exec();
						$cid = array();
						for ($req; ! $req->EOF; $req->MoveNext()) {
							$cid[] = $req->fields['contact_id'];
							$titulo1[] = $req->fields['contact_first_name']
							. ' ' . $req->fields['contact_last_name'];
						}
						echo implode(',', $cid);
		?>'>
				&nbsp;&nbsp;&nbsp;<input type='checkbox' onmouseover="window.status = '<?php echo addslashes(implode(',', $titulo1)); ?>';" 
				 onmouseout="window.status = '';"
				name='email_task_contacts' id='email_task_contacts' <?php
				   if ($tt)
						echo "checked='checked'";
						?>><?php echo $AppUI->_('Task Contacts');?>
			</br>			
		<!----------------------------------------------------------------------------------------------------------------->			
						
						
				<input type='hidden' name='email_project_list' id='email_project_list'
				  value='<?php
						$q->clear();
						$q->addTable('project_contacts', 'pc');
						$q->leftJoin('contacts', 'c', 'c.contact_id = pc.contact_id');
						$q->addWhere("pc.project_id = '$obj->task_project'");
						$q->addQuery('pc.contact_id');
						$q->addQuery('c.contact_first_name, c.contact_last_name');
						$req =& $q->exec();
						$cid = array();
						$titulo2 = array();
						for ($req; ! $req->EOF; $req->MoveNext()) {
							if (! in_array($req->fields['contact_id'], $cid)) {
							  $cid[] = $req->fields['contact_id'];
							  $titulo2[] = $req->fields['contact_first_name']
							  . ' ' . $req->fields['contact_last_name'];
							}
						}
						echo implode(',', $cid);
						$q->clear();
		?>'>
		&nbsp;&nbsp;&nbsp;<input type='checkbox' onmouseover="window.status = '<?php echo addslashes(implode(',', $titulo2)); ?>';" 
				 onmouseout="window.status = '';"
				 name='email_project_contacts' id='email_project_contacts' <?php
				   if ($tp)
						echo "checked='checked'";
						?>><?php echo $AppUI->_('Project Contacts');?>
			</br>
		<!----------------------------------------------------------------------------------------------------------------->
		
				
				<input type='hidden' name='email_dep_list' id='email_dep_list'
				  value='<?php
						$q->clear();
						$q->addTable('task_dependencies', 'td');
						$q->innerJoin('tasks', 't', 'td.dependencies_task_id=t.task_id');
						$q->leftJoin('users', 'u', 't.task_owner=u.user_id');
						$q->leftJoin('contacts','c','c.contact_id = u.user_contact');
						$q->addWhere("td.dependencies_req_task_id= '$obj->task_id'");
						$q->addQuery('c.contact_id');
						$q->addQuery('c.contact_first_name, c.contact_last_name');
						$q->addQuery('t.task_name');
						$req =& $q->exec();
						$res=mysql_query($sql);
						$cid = array();
						$titulo3 = array();
						for ($req; ! $req->EOF; $req->MoveNext()) {
							if (! in_array($req->fields['contact_id'], $cid)) {
							$cid[] = $req->fields['contact_id'];
							  $titulo3[] = $req->fields['task_name'] . '::' . $req->fields['contact_first_name']
							  . ' ' . $req->fields['contact_last_name'];
							}
						}
						echo implode(',', $cid);
						$q->clear();
		?>'>
		&nbsp;&nbsp;&nbsp;<input type='checkbox'  onmouseover="window.status = '<?php echo addslashes(implode(',', $titulo3)); ?>';" 
				 onmouseout="window.status = '';"
				name='email_dep_ch' id='email_dep_ch' >
		<?php echo $AppUI->_('etiq_Dependientes');?>
		
	
		</br>
		<!----------------------------------------------------------------------------------------------------------------->
		
				
				<input type='hidden' name='email_others' id='email_others' value=''>
				
			
		<!----------------------------------------------------------------------------------------------------------------->
		
		<?php
					if ($AppUI->isActiveModule('contacts') && $perms->checkModule('contacts', 'view')) {
				?><br>&nbsp;&nbsp;&nbsp;
				<input type='button' class='button' value='<?php echo $AppUI->_('Other Contacts...');?>' onclick='javascript:popEmailContacts();' />
		
				<?php } ?>
			</td>
		</tr>

	
	
<?php
	}
?>
</table>
</td>
<td width='60%' valign='top' align='center'>
<table width='100%'>
<tr>
	<td align="right"><?php echo $AppUI->_('Summary');?>:</td>
        <td valign="middle">
                <table width="100%">
                        <tr>
                                <td align="left">
                                        <input type="text" class="text" name="task_log_name" id="task_log_name_text" value="" maxlength="255" size="30" onBlur="javascript:cam(this.value)" />
                                </td>
                                <td align="center"><?php echo $AppUI->_('Problem');?>:
                                        <input type="checkbox" value="1" name="task_log_problem" <?php if($log->task_log_problem){?>checked="checked"<?php }?> />
                                </td>
                        </tr>
                </table>
	<br></td>
</tr>
<input type=hidden name="task_log_reference" value=0>


<!--ina-->


<tr>
	<td align="right" valign="top"><?php echo $AppUI->_('Description');?>:</td>
	<td align="left">
		<textarea name="task_log_description" class="textarea" cols="50" rows="6"><?php echo htmlspecialchars($log->task_log_description);?></textarea>
	</td>
</tr>
<tr>
	<td align="right" valign="top"><br/><?php echo $AppUI->_('Extra Recipients');?>:</td>
	<td align="left"><br/>
		<input type="text" class="text" name="email_extras" maxlength="255" size="30" />
	</td>
</tr>
<tr>
	<td colspan="2" valign="bottom" align="right">
		<input type="button" class="button" value="<?php echo $AppUI->_('update task');?>" onclick=" comprobar ()" />
	</td>
</tr>
</td>
</table>
</td>
</tr>
</table>
<script>
function comprobar()  {
		var f = document.editFrm;
		var fech = f.task_log_date.value.substr(0,10);
		var arr = fech.split('-');
		var fecha = arr[0] + arr[1] + arr[2];
		fecha = parseInt(fecha.substr(0,8));
		var initarea = <?echo str_replace('-','',substr($obj->task_start_date_ir,0,10))?>;
		if ( initarea > fecha ) alert ( 'Debe introducir una fecha posterior al inicio real de la tarea');
		else updateTask();
	}
</script>
</form>