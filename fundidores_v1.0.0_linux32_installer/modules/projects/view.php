<?php /* PROJECTS $Id: view.php,v 1.95 2006/04/11 17:21:14 Attest sw-libre@attest.es  Exp $ */
/* PROJECTS $Id: view.php,v 1.94 2005/04/04 12:56:56 gregorerhardt Exp $ */
$project_id = intval( dPgetParam( $_GET, "project_id", 0 ) );

// check permissions for this record
$perms =& $AppUI->acl();
$canRead = $perms->checkModuleItem( $m, 'view', $project_id );
$canEdit = $perms->checkModuleItem( $m, 'edit', $project_id );

if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// retrieve any state parameters
if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'ProjVwTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'ProjVwTab' ) !== NULL ? $AppUI->getState( 'ProjVwTab' ) : 0;

// check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CProject();
// Now check if the proect is editable/viewable.
$denied = $obj->getDeniedRecords($AppUI->user_id);
if (in_array($project_id, $denied)) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$canDelete = $obj->canDelete( $msg, $project_id );

// get critical tasks (criteria: task_end_date)
$criticalTasks = ($project_id > 0) ? $obj->getCriticalTasks($project_id) : NULL;

// get ProjectPriority from sysvals
$projectPriority = dPgetSysVal( 'ProjectPriority' );
$projectPriorityColor = dPgetSysVal( 'ProjectPriorityColor' );

$working_hours = $dPconfig['daily_working_hours'];

// load the record data
// GJB: Note that we have to special case duration type 24 and this refers to the hours in a day, NOT 24 hours
$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery("company_name,
	CONCAT_WS(' ',contact_first_name,contact_last_name) user_name,
	projects.*,
	SUM(t1.task_duration * t1.task_percent_complete * IF(t1.task_duration_type = 24, ".$working_hours.", t1.task_duration_type))/
		SUM(t1.task_duration * IF(t1.task_duration_type = 24, ".$working_hours.", t1.task_duration_type)) AS project_percent_complete");
$q->addJoin('companies', 'com', 'company_id = project_company');
$q->addJoin('users', 'u', 'user_id = project_owner');
$q->addJoin('contacts', 'con', 'contact_id = user_contact');
$q->addJoin('tasks', 't1', 'projects.project_id = t1.task_project');
$q->addWhere('project_id = '.$project_id);
$q->addGroup('project_id');
$sql = $q->prepare();
$q->clear();

$obj = null;
if (!db_loadObject( $sql, $obj )) {
	$AppUI->setMsg( 'Project' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
} else {
	$AppUI->savePlace();
}


// worked hours
// now milestones are summed up, too, for consistence with the tasks duration sum
// the sums have to be rounded to prevent the sum form having many (unwanted) decimals because of the mysql floating point issue
// more info on http://www.mysql.com/doc/en/Problems_with_float.html
$q->addTable('task_log');
$q->addTable('tasks');
$q->addQuery('ROUND(SUM(task_log_hours),2)');
$q->addWhere("task_log_task = task_id AND task_project = $project_id");
$sql = $q->prepare();
$q->clear();
$worked_hours = db_loadResult($sql);
$worked_hours = rtrim($worked_hours, "0");

// total hours
// same milestone comment as above, also applies to dynamic tasks
$q->addTable('tasks');
$q->addQuery('ROUND(SUM(task_duration),2)');
$q->addWhere("task_project = $project_id AND task_duration_type = 24 AND task_dynamic != 1");
$sql = $q->prepare();
$q->clear();
$days = db_loadResult($sql);

$q->addTable('tasks');
$q->addQuery('ROUND(SUM(task_duration),2)');
$q->addWhere("task_project = $project_id AND task_duration_type = 1 AND task_dynamic != 1");
$sql = $q->prepare();
$q->clear();
$hours = db_loadResult($sql);
$total_hours = $days * $dPconfig['daily_working_hours'] + $hours;

$total_project_hours = 0;

$q->addTable('tasks', 't');
$q->addQuery('ROUND(SUM(t.task_duration*u.perc_assignment/100),2)');
$q->addJoin('user_tasks', 'u', 't.task_id = u.task_id');
$q->addWhere("t.task_project = $project_id AND t.task_duration_type = 24 AND t.task_dynamic != 1");
$total_project_days_sql = $q->prepare();
$q->clear();

$q->addTable('tasks', 't');
$q->addQuery('ROUND(SUM(t.task_duration*u.perc_assignment/100),2)');
$q->addJoin('user_tasks', 'u', 't.task_id = u.task_id');
$q->addWhere("t.task_project = $project_id AND t.task_duration_type = 1 AND t.task_dynamic != 1");
$total_project_hours_sql = $q->prepare();
$q->clear();

$total_project_hours = db_loadResult($total_project_days_sql) * $dPconfig['daily_working_hours'] + db_loadResult($total_project_hours_sql);
//due to the round above, we don't want to print decimals unless they really exist
//$total_project_hours = rtrim($total_project_hours, "0");

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');

// create Date objects from the datetime fields
$start_date = intval( $obj->project_start_date ) ? new CDate( $obj->project_start_date ) : null;
$end_date = intval( $obj->project_end_date ) ? new CDate( $obj->project_end_date ) : null;
$actual_end_date = intval( $criticalTasks[0]['task_end_date'] ) ? new CDate( $criticalTasks[0]['task_end_date'] ) : null;
$style = (( $actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '';

// setup the title block
$titleBlock = new CTitleBlock( 'View Project', 'applet3-48.png', $m, "$m.$a" );

// patch 2.12.04 text to search entry box
if (isset( $_POST['searchtext'] )) {
	$AppUI->setState( 'searchtext', $_POST['searchtext']);
}

$search_text = $AppUI->getState('searchtext') ? $AppUI->getState('searchtext'):'';
ob_start();
?>

<br>
<form action="?m=projects&a=view&project_id=<?echo $project_id;?>" method="post" name="searchfilter">
<table class="std" title="<?echo $AppUI->_('Search in name and description fields')?>" width=300 height=60>
	<tr>
		<td width=20>&nbsp;</td>
		<td>
			<?echo $AppUI->_("Search").' '.$AppUI->_("Task");?>:&nbsp;&nbsp;
		
			<input type="text" class="text" SIZE="10" name="searchtext"  value="<?echo $search_text?>" />
			<input type="submit" class="button" value="<?echo $AppUI->_("Search")?>" />
		</td>
	</tr>
</table>
</form>
<?
$buff = ob_get_clean();


if ($canEdit) {
	$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new task').'">', '',
		'<form action="?m=tasks&a=addedit&task_project=' . $project_id . '" method="post">', '</form>'
	);
	$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new file').'">', '',
		'<form action="?m=files&a=addedit&project_id=' . $project_id . '" method="post">', '</form>'
	);
}
$titleBlock->show();


$titleBlock= new CTitleBlock('');

$titleBlock->addCrumb( "?m=projects", "projects list" );
if ($canEdit) {
	$titleBlock->addCrumb( "?m=projects&a=addedit&project_id=$project_id", "edit this project" );
	if ($canDelete) {
		$titleBlock->addCrumbDelete( 'delete project', $canDelete, $msg );
	}
	
}
$titleBlock->addCrumb( "javascript:mostrarOcultar('detalles','tareas',235)", "mostrar/ocultar detalles del proyecto" );
//ina 
$titleBlock->show();
?>


<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
	?>
	function delIt() {
		if (confirm( "<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS)?>" )) {
			document.frmDelete.submit();
		}
	}
	<?php 
} ?>
/*
function mostrarOcultar (id,otroid,desplazamiento) {
	
	var capa = window.document.getElementById(id).style;
	var capa2= window.document.getElementById(otroid).style;
	var pos;
	if (capa.visibility=="hidden") {
		capa.visibility="visible";
		pos = parseInt(capa2.top);
		capa2.top = pos + desplazamiento;
		
	}
	else {
		capa.visibility="hidden";	
		pos = parseInt(capa2.top);
		capa2.position = "absolute";
		capa2.top = pos - desplazamiento;
		
	}
}*/
function mostrarOcultar (id,otroid,desplazamiento) {
	
	var capa = window.document.getElementById(id).style;
	var capa2= window.document.getElementById(otroid).style;
	var pos;
	if (capa.visibility=="hidden") {
		capa.visibility="visible";
		capa2.position = "static";
		//eval(Refcapa+capa+Refestilo+Reftop+'='+desplazamiento)
	}
	else {
		capa.visibility="hidden";	
		pos = parseInt(capa2.top);
		capa2.position = "absolute"
		capa2.top = 175;
	}
	
}

</script>
<div id="detalles" style="visibility:hidden;position:static">
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">

	<form name="frmDelete" action="./index.php?m=projects" method="post">
		<input type="hidden" name="dosql" value="do_project_aed" />
		<input type="hidden" name="del" value="1" />
		<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
	</form>
	
	<tr>
		<td style="border: outset #d1d1cd 1px;background-color:#<?php echo $obj->project_color_identifier;?>" colspan="2">
		<?php
			echo '<font color="' . bestColor( $obj->project_color_identifier ) . '"><strong>'
				. $obj->project_name .'<strong></font>';
		?>
		</td>
	</tr>
	
	<tr>
		<td width="50%" valign="top">
			<strong><?php echo utf8_encode (strtoupper($AppUI->_('Details')));?></strong>
			<table cellspacing="1" cellpadding="2" border="0" width="100%">
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Company');?>:</td>
				<td class="hilite" width="100%"><?php echo htmlspecialchars( $obj->company_name, ENT_QUOTES) ;?></td>
			</tr>
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Short Name');?>:</td>
				<td class="hilite"><?php echo htmlspecialchars( @$obj->project_short_name, ENT_QUOTES) ;?></td>
			</tr>
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('etiq_Moldista');?>:</td>
				<td class="hilite">
				<?php 
				if ($obj->project_mold != 0) {
					$q=new DBQuery();
					$q->addTable('companies');
					$q->addQuery('company_name');
					$q->addWhere('company_id='.$obj->project_mold);
					$q->exec();
					$name = $q->loadResult();
					$q->clear();
					echo $name;
					
				}	
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('etiq_Cliente');?>:</td>
				<td class="hilite">
				<?php 
				if ($obj->project_client != 0) {
					$q=new DBQuery();
					$q->addTable('companies');
					$q->addQuery('company_name');
					$q->addWhere('company_id='.$obj->project_client);
					$q->exec();
					$name = $q->loadResult();
					$q->clear();
					echo $name;
					
				}	
				?>
				</td>
			</tr>
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Start Date');?>:</td>
				<td class="hilite"><?php echo $start_date ? $start_date->format( $df ) : '-';?></td>
			</tr>
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Target End Date');?>:</td>
				<td class="hilite"><?php echo $end_date ? $end_date->format( $df ) : '-';?></td>
			</tr>
			
			<!--
			
			-->
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('etiq_resp');?>:</td>
				<td class="hilite"><?php echo $obj->user_name; ?></td>
			</tr>
			<!--
			
			-->
			<tr>
				<td colspan="2">
				<?php
					require_once("./classes/CustomFields.class.php");
					$custom_fields = New CustomFields( $m, $a, $obj->project_id, "view" );
					$custom_fields->printHTML();
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2"><br/>
				<strong><?php echo utf8_encode (strtoupper($AppUI->_('Description')));?></strong><br />
				<table cellspacing="0" cellpadding="2" border="0" width="100%">
				<tr>
					<td class="hilite">
						<?php echo str_replace( chr(10), "<br>", $obj->project_description) ; ?>&nbsp;
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
		</td>
		<td width="50%" rowspan="9" valign="top">
			<strong><?php echo utf8_encode (strtoupper($AppUI->_('Summary')));?></strong><br />
			<table cellspacing="1" cellpadding="2" border="0" width="100%">
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Status');?>:</td>
				<td class="hilite" width="100%"><?php echo $AppUI->_($pstatus[$obj->project_status]);?></td>
			</tr>
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Priority');?>:</td>
				<td class="hilite" width="100%" style="background-color:<?php echo $projectPriorityColor[$obj->project_priority]?>"><?php echo $AppUI->_($projectPriority[$obj->project_priority]);?></td>
			</tr>
			
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Progress');?>:</td>
				<td class="hilite" width="100%"><?php printf( "%.1f%%", $obj->project_percent_complete );?></td>
			</tr>
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Active');?>:</td>
				<td class="hilite" width="100%"><?php echo $obj->project_active ? $AppUI->_('Yes') : $AppUI->_('No');?></td>
			</tr>
			
			<tr>
				<td align="left" nowrap>&nbsp;&nbsp;&nbsp;<?php echo $AppUI->_('Scheduled Hours');?>:</td>
				<td class="hilite" width="100%"><?php echo $total_hours ?></td>
			</tr>
							
			<?php
			$q  = new DBQuery;
			$q->addTable('departments', 'a');
			$q->addTable('project_departments', 'b');
			$q->addQuery('a.dept_id, a.dept_name');
			$q->addWhere("a.dept_id = b.department_id and b.project_id = $project_id");
			$depts = $q->loadHashList("dept_id");
			if (count($depts) > 0) {
			?>
			    <tr>
			    	<td><strong><?php echo utf8_encode (strtoupper($AppUI->_("Departments"))); ?></strong></td>
			    </tr>
			    <tr>
			    	<td colspan='3' class="hilite">
			    		<?php
			    			foreach($depts as $dept_id => $dept_info){
			    				echo "<div>".$dept_info["dept_name"];
			    				if($dept_info["dept_phone"] != ""){
			    					echo "( ".$dept_info["dept_phone"]." )";
			    				}
			    				echo "</div>";
			    			}
			    		?>
			    	</td>
			    </tr>
		 		<?php
			}
			
				$q  = new DBQuery;
				$q->addTable('contacts', 'a');
				$q->addTable('project_contacts', 'b');
				$q->addQuery('a.contact_id, a.contact_first_name, a.contact_last_name,
						a.contact_email, a.contact_phone, d.dept_name as contact_department');
				$q->addJoin('departments','d','a.contact_department=d.dept_id');
				$q->addWhere("a.contact_id = b.contact_id and b.project_id = $project_id
						and (contact_owner = '$AppUI->user_id' or contact_private='0')");
	
				$contacts = $q->loadHashList("contact_id");
				if(count($contacts)>0){
					?>
				    <tr>
				    	<td><strong><?php echo utf8_encode (strtoupper($AppUI->_("Contacts"))); ?></strong></td>
				    </tr>
				    <tr>
				    	<td colspan='3' class="hilite">
				    		<?php
				    			echo "<table cellspacing='1' cellpadding='2' border='0' width='100%' bgcolor='black'>";
				    			echo "<tr><th>".$AppUI->_("Name")."</th><th>".$AppUI->_("Email")."</th><th>".$AppUI->_("Phone")."</th><th>".$AppUI->_("Department")."</th></tr>";
				    			foreach($contacts as $contact_id => $contact_data){
				    				echo "<tr>";
				    				echo "<td class='hilite'>";
								$canEdit = $perms->checkModuleItem('contacts', 'edit', $contact_id);
								if ($canEdit)
									echo "<a href='index.php?m=contacts&a=view&contact_id=$contact_id'>";
								echo $contact_data["contact_first_name"]." ".$contact_data["contact_last_name"];
								if ($canEdit)
									echo "</a>";
								echo "</td>";
				    				echo "<td class='hilite'><a href='mailto: ".$contact_data["contact_email"]."'>".$contact_data["contact_email"]."</a></td>";
				    				echo "<td class='hilite'>".$contact_data["contact_phone"]."</td>";
				    				echo "<td class='hilite'>".$contact_data["contact_department"]."</td>";
				    				echo "</tr>";
				    			}
				    			echo "</table>";
				    		?>
				    	</td>
				    </tr>
				    <tr>
				    	<td>
			 <?php
			}?>
			</table>
	</td>
</tr>
</table>

</div>
<div id="tareas" style="position:absolute;top:175px">
<?
if ($_GET["tab"]<3) {
?>
<table width=100% border=0><tr><td width=1%>&nbsp;</td><td>
<div align=left><?=$buff?> </div>
</td></tr></table>
<?}?>
<?php

$tabBox = new CTabBox( "?m=projects&a=view&project_id=$project_id", "", $tab );
$query_string = "?m=projects&a=view&project_id=$project_id";
// tabbed information boxes
// Note that we now control these based upon module requirements.
$canViewTask = $perms->checkModule('tasks', 'view');
if ($canViewTask) {
	$tabBox->add( dPgetConfig('root_dir')."/modules/tasks/tasks", 'Tasks' );
	$tabBox->add( dPgetConfig('root_dir')."/modules/tasks/tasks", 'Tasks (Inactive)' );
}
/*if ($perms->checkModule('forums', 'view'))
	$tabBox->add( dPgetConfig('root_dir')."/modules/projects/vw_forums", 'Forums' );
*/
//if ($perms->checkModule('files', 'view'))
//	$tabBox->add( dPgetConfig('root_dir')."/modules/projects/vw_files", 'Files' );
if ($canViewTask) {
	$tabBox->add( dPgetConfig('root_dir')."/modules/tasks/viewgantt", 'Gantt Chart' );
	$tabBox->add( dPgetConfig('root_dir')."/modules/projects/vw_logs", 'Task Logs' );
}
$tabBox->loadExtras($m);
$f = 'all';
$min_view = true;

$tabBox->show();



?></div>
</div>

</table>