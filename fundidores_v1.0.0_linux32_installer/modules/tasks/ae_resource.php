<?php // $Id: ae_resource.php,v 1.6 2006/03/14 12:59:52 Attest sw-libre@attest.es Exp $
// $Id: ae_resource.php,v 1.5 2005/03/19 05:58:52 ajdonnison Exp $
global $AppUI, $users, $task_id, $task_project, $obj, $projTasksWithEndDates, $tab, $loadFromTab;
$jp=1;
if ( $task_id == 0 ) {
	// Add task creator to assigned users by default
	//$assigned_perc = array($AppUI->user_id => "100");	
	$jp=0;
} else {
	// Pull users on this task
//			 SELECT u.user_id, CONCAT_WS(' ',u.user_first_name,u.user_last_name)
	$sql = "
			 SELECT user_id, perc_assignment
			   FROM user_tasks
			 WHERE task_id =$task_id
			 AND task_id <> 0
			 ";
	$assigned_perc = db_loadHashList( $sql );	
}

$initPercAsignment = "";
$assigned = array();
if ($jp) {foreach ($assigned_perc as $user_id => $perc) {
	//ina  $assigned[$user_id] = $users[$user_id] . " [" . $perc . "%]";
	$assigned[$user_id] = $users[$user_id] ;
	$initPercAsignment .= "$user_id=$perc;";
}
}
function next_working_day( $dateObj ) {
		global $AppUI;
		$end = intval(dPgetConfig('cal_day_end'));
		$start = intval(dPgetConfig('cal_day_start'));
		$dateObj->addDays(1);
		while ( ! $dateObj->isWorkingDay() || $dateObj->getHour() >= $end ) {
			
			$dateObj->setTime($start, '0', '0');$dateObj->addDays(1);
		}
		return $dateObj;
	}
function isWorkingDay(){
		global $AppUI;
		
		$working_days = dPgetConfig("cal_working_days");
		if(is_null($working_days)){
			$working_days = array('1','2','3','4','5');
		} else {
			$working_days = explode(",", $working_days);
		}
		
		return in_array($this->getDayOfWeek(), $working_days);
	}
?>
<script language="javascript">
<?php
echo "var projTasksWithEndDates=new Array();\n";
$keys = array_keys( $projTasksWithEndDates );
for ($i = 1; $i < sizeof($keys); $i++) {
	//array[task_is] = end_date, end_hour, end_minutes
	
	//ina
	$d = $projTasksWithEndDates[$keys[$i]][1];
	$ad = explode('/',$d);
	$date = new CDate($ad[2]."-".$ad[1]."-".$ad[0].' 08:00:00');
	$date = next_working_day ( $date ) ;
	$string_date = substr($date->getDate(),0,10);
	$arr_date = explode('-',$string_date);
	$string_date = $arr_date[2].'/'.$arr_date[1].'/'.$arr_date[0];

	
	echo "projTasksWithEndDates[".$keys[$i]."]=new Array(\"".$string_date."\", \"".$projTasksWithEndDates[$keys[$i]][2]."\", \"".$projTasksWithEndDates[$keys[$i]][3]."\");\n";
}
?>
</script>
<form action="?m=tasks&a=addedit&task_project=<?php echo $task_project; ?>"
  method="post" name="resourceFrm">
<input type="hidden" name="sub_form" value="1" />
<input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
<input type="hidden" name="dosql" value="do_task_aed" />
	<input name="hperc_assign" type="hidden" value="<?php echo
	$initPercAsignment;?>"/>
<table width="100%" border="1" cellpadding="4" cellspacing="0" class="std">
<tr>
	<td valign="top" align="center">
		<table cellspacing="0" cellpadding="2" border="0">
			<tr>
				<td><?php echo $AppUI->_( 'Human Resources' );?>:</td>
				<td><?php echo $AppUI->_( 'Assigned to Task' );?>:</td>
			</tr>
			<tr>
				<td>
					<?php echo arraySelect( $users, 'resources', 'style="width:220px" size="10" class="text" multiple="multiple" ', null ); ?>
				</td>
				<td>
					<?php echo arraySelect( $assigned, 'assigned', 'style="width:220px" size="10" class="text" multiple="multiple" ', null ); ?>
				</td>
			<tr>
				<td colspan="2" align="center">
					<table>
					<tr>
						<td align="right"><input type="button" class="button" value="&gt;" onClick="addUser(document.resourceFrm)" /></td>
						<td>
						<input type="hidden" name="percentage_assignment" value ="100"> 	
						<!--<select name="percentage_assignment" class="text">
							<?php /*
								for ($i = 5; $i <= 100; $i+=5) {
									echo "<option ".(($i==100)? "selected=\"true\"" : "" )." value=\"".$i."\">".$i."%</option>";
								}
							*/?>
							</select>-->
						</td>				
						<td align="left"><input type="button" class="button" value="&lt;" onClick="removeUser(document.resourceFrm)" /></td>					
					</tr>
					</table>
				</td>
			</tr>
			</tr>
 			
		</table>
	</td>
	<td valign="top" align="center">
		<table><tr><td align="left">
		<?php echo $AppUI->_( 'Additional Email Comments' );?>:		
		<br />
		<textarea name="email_comment" class="textarea" cols="60" rows="10" wrap="virtual"></textarea><br />
		<input type="checkbox" name="task_notify" value="1"  /> <?php echo $AppUI->_( 'notifyChange' );?>
		</td></tr></table><br />
		
	</td>
</tr>
</table>
<input type="hidden" name="hassign" />
</form>
<script language="javascript">
  subForm.push(new FormDefinition(<?php echo $tab; ?>, document.resourceFrm, checkResource, saveResource));
</script>
