<?php /* PROJECTS $Id: vw_idx_archived.php,v 1.2 2006/04/06 12:40:19 Attest sw-libre@attest.es Exp $ */
/* PROJECTS $Id: vw_idx_archived.php,v 1.19 2005/03/28 03:32:34 gregorerhardt Exp $ */
GLOBAL $AppUI, $projects, $company_id;
$perms =& $AppUI->acl();
$df = $AppUI->getPref('SHDATEFORMAT');
?>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>&nbsp;</tr>
<tr>
        <th nowrap="nowrap">
               <?php echo $AppUI->_('Color');?></a>
        </th>
         <th nowrap="nowrap" class="hdr"><?php echo $AppUI->_('Ref');?></th>
	<th nowrap>
		<?php echo $AppUI->_('Project Name');?></a>
	</th>
	<th nowrap="nowrap">
	<?php echo $AppUI->_('etiq_resp');?></a>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Tasks');?></a>
	</th>
	<th nowrap="nowrap">
	<?php echo $AppUI->_('Finished');?></a>
	</th>
</tr>

<?php
$CR = "\n";
$CT = "\n\t";
$none = true;
foreach ($projects as $row) {
	if (! $perms->checkModuleItem('projects', 'view', $row['project_id']))
		continue;
	if ($row["project_active"] < 1) {
		$none = false;
		$end_date = intval( @$row["project_actual_end_date"] ) ? new CDate( $row["project_actual_end_date"] ) : null;
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
		$s .= $CR . '<td nowrap="nowrap">' . htmlspecialchars( $row["user_username"], ENT_QUOTES ) . '</td>';
		$s .= $CR . '<td align="center" nowrap="nowrap">';
		$s .= $CT . $row["total_tasks"];
		$s .= $CR . '</td>';
		$s .= $CR . '<td align="right" nowrap="nowrap">';
		$s .= $CT . ($end_date ? $end_date->format( $df ) : '-');
		$s .= $CR . '</td>';
		$s .= $CR . '</tr>';
		echo $s;
	}
}
if ($none) {
	echo $CR . '<tr><td colspan="6">' . $AppUI->_( 'No projects available' ) . '</td></tr>';
}
?>
<tr>
	<td colspan="6">&nbsp;</td>
</tr>
</table>
