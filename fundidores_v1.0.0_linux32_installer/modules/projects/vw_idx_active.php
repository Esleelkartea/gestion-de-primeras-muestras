<?php /* PROJECTS $Id: vw_idx_active.php,v 1.29 2006/04/06 13:59:23 Attest sw-libre@attest.es Exp $ */
/* PROJECTS $Id: vw_idx_active.php,v 1.28 2005/03/28 03:32:34 gregorerhardt Exp $ */
global $projects;
global $AppUI, $company_id, $priority;

$perms =& $AppUI->acl();
$df = $AppUI->getPref('SHDATEFORMAT');
?>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">

<tr>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Color');?></a>
	</th>
	 <th nowrap="nowrap" class="hdr"><?php echo $AppUI->_('Ref');?></th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Project Name');?>
	</th>
          <th nowrap="nowrap">
><?php echo $AppUI->_('Start');?>
	</th>
        <th nowrap="nowrap">
	<?php echo $AppUI->_('Due Date');?>
	</th>
        <th nowrap="nowrap">
		<?php echo $AppUI->_('Actual');?>
	</th>
        <th nowrap="nowrap">
	<?php echo $AppUI->_('P');?>
	</th>
	<th nowrap="nowrap">
	<?php echo $AppUI->_('etiq_resp');?>
	</th>
	<th nowrap="nowrap">
<?php echo $AppUI->_('Tasks');?>
		
	</th>
</tr>

<?php
$CR = "\n";
$CT = "\n\t";
$none = true;
foreach ($projects as $row) {
	if (! $perms->checkModuleItem('projects', 'view', $row['project_id']))
		continue;
	// We dont check the percent_completed == 100 because some projects
	// were being categorized as completed because not all the tasks
	// have been created (for new projects)
	if ($row["project_active"] > 0 && $row["project_status"] == 3) {
		$none = false;
                $start_date = intval( @$row["project_start_date"] ) ? new CDate( $row["project_start_date"] ) : null;
		$end_date = intval( @$row["project_end_date"] ) ? new CDate( $row["project_end_date"] ) : null;
                $actual_end_date = intval( @$row["project_actual_end_date"] ) ? new CDate( $row["project_actual_end_date"] ) : null;
                $style = (( $actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '';

		$s = '<tr>';
		$s .= '<td width="65" align="center" style="border: outset #eeeeee 2px;background-color:#'
			. $row["project_color_identifier"] . '">';
		$s .= $CT . '<font color="' . bestColor( $row["project_color_identifier"] ) . '">'
			. sprintf( "%.1f%%", $row["project_percent_complete"] )
			. '</font>';
		$s .= $CR . '</td>';
		$s .= $CR . '<td width="10%">';
		$s .= $CT .  htmlspecialchars( $row["project_short_name"], ENT_QUOTES ) ;
		$s .= $CR . '</td>';

		$s .= $CR . '<td width="100%">';
		$s .= $CT . '<a href="?m=projects&a=view&project_id=' . $row["project_id"] . '" title="' . htmlspecialchars( $row["project_description"], ENT_QUOTES ) . '">' . htmlspecialchars( $row["project_name"], ENT_QUOTES ) . '</a>';
		$s .= $CR . '</td>';
                $s .= $CR . '<td align="center">'. ($start_date ? $start_date->format( $df ) : '-') .'</td>';
                $s .= $CR . '<td align="right" nowrap="nowrap" style="background-color:'.$priority[$row['project_priority']]['color'].'">';
		$s .= $CT . ($end_date ? $end_date->format( $df ) : '-');
		$s .= $CR . '</td>';
                $s .= $CR . '<td align="center">';
                $s .= $actual_end_date ? '<a href="?m=tasks&a=view&task_id='.$row["critical_task"].'">' : '';
                $s .= $actual_end_date ? '<span '. $style.'>'.$actual_end_date->format( $df ).'</span>' : '-';
                $s .= $actual_end_date ? '</a>' : '';
		$s .= $CR . '</td>';
                $s .= $CR . '<td align="center">';
                $s .= $row["task_log_problem"] ? '<a href="?m=tasks&a=index&f=all&project_id='.$row["project_id"].'">' : '';
                $s .= $row["task_log_problem"] ? dPshowImage( './images/icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem' ): '-';
                $s .= $CR . $row["task_log_problem"] ? '</a>' : '';
                $s .= $CR . '</td>';
		$s .= $CR . '<td nowrap="nowrap">' . htmlspecialchars( $row["user_username"], ENT_QUOTES ) . '</td>';
		$s .= $CR . '<td align="center" nowrap="nowrap">';
		$s .= $CT . $row["total_tasks"] . ($row["my_tasks"] ? ' ('.$row["my_tasks"].')' : '');
		$s .= $CR . '</td>';
		$s .= $CR . '</tr>';
		echo $s;
	}
}
if ($none) {
	echo $CR . '<tr><td colspan="10">' . $AppUI->_( 'No projects available' ) . '</td></tr>';
}
?>
<tr>
	<td colspan="8">&nbsp;</td>
</tr>
</table>
