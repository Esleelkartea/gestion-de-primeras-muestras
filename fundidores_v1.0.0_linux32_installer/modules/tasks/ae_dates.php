<?php // $Id: ae_dates.php,v 1.9 2006/03/16 12:41:41 Attest sw-libre@attest.es Exp $
// $Id: ae_dates.php,v 1.8 2005/04/08 04:00:41 ajdonnison Exp $

global $AppUI, $dPconfig, $task_parent_options, $loadFromTab;
global $can_edit_time_information, $locale_char_set, $obj;
global $durnTypes, $task_project, $task_id, $tab;

//Time arrays for selects
$start = intval(dPgetConfig('cal_day_start'));
$end   = intval(dPgetConfig('cal_day_end'));
$inc   = intval(dPgetConfig('cal_day_increment'));
if ($start === null ) $start = 8;
if ($end   === null ) $end = 17;
if ($inc   === null)  $inc = 15;
$hours = array();
for ( $current = $start; $current < $end + 1; $current++ ) {
	if ( $current < 10 ) { 
		$current_key = "0" . $current;
	} else {
		$current_key = $current;
	}
	
	if ( stristr($AppUI->getPref('TIMEFORMAT'), "%p") ){
		//User time format in 12hr
		$hours[$current_key] = ( $current > 12 ? $current-12 : $current );
	} else {
		//User time format in 24hr
		$hours[$current_key] = $current;
	}
}

$minutes = array();
$minutes["00"] = "00";
for ( $current = 0 + $inc; $current < 60; $current += $inc ) {
	$minutes[$current] = $current;
}

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

$start_date = intval( $obj->task_start_date ) ? new CDate( $obj->task_start_date ) : new CDate();
$end_date = intval( $obj->task_end_date ) ? new CDate( $obj->task_end_date ) : null;

$start_date_ir = intval( $obj->task_start_date_ir ) ? new CDate( $obj->task_start_date_ir ) : null;
$end_date_ir = intval( $obj->task_end_date_ir ) ? new CDate( $obj->task_end_date_ir ) : null;


// convert the numeric calendar_working_days config array value to a human readable output format
$cwd = explode(',', $dPconfig['cal_working_days']);
$cwd_conv = array_map( 'cal_work_day_conv', $cwd );
$cwd_hr = implode(', ', $cwd_conv);
?>
<script>

function copiarFechas() {
	var start = document.getElementById('task_start_date');	
	var s = document.getElementById('start_date');	
	var end = document.getElementById('task_end_date');	
	var e = document.getElementById('end_date');	
	
	var startReal = document.getElementById('task_start_date_ir');	
	var sr = document.getElementById('start_date_ir');
	var endReal = document.getElementById('task_end_date_ir');	
	var er = document.getElementById('end_date_ir');	
	startReal.value = start.value;
	sr.value = s.value;
	
	endReal.value = end.value;
	er.value = e.value;

	
}


</script>
<?

function cal_work_day_conv($val) {
	GLOBAL $locale_char_set;
	$wk = Date_Calc::getCalendarWeek( null, null, null, "%a", LOCALE_FIRST_DAY );
	return htmlentities($wk[$val], ENT_COMPAT, $locale_char_set);
}
?>
<form name="datesFrm" action="?m=tasks&a=addedit&task_project=<?php echo $task_project;?>" method="post">
<input name="dosql" type="hidden" value="do_task_aed" />
<input name="task_id" type="hidden" value="<?php echo $task_id;?>" />
<input name="sub_form" type="hidden" value="1" />
<table width="100%" border="0" cellpadding="4" cellspacing="0" class="std">
<?php
	if($can_edit_time_information){
?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Start Date' );?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="task_start_date" id="task_start_date" value="<?php echo $start_date ? $start_date->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
		<input type="text" name="start_date" id="start_date" value="<?php echo $start_date ? $start_date->format( $df ) : "" ;?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar(document.datesFrm.start_date)">
						<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0">
					</a>
	</td>
	
	<td align=right nowrap="nowrap"><?php echo $AppUI->_( 'Start Date' );?> REAL</td><td>
		<input type="hidden" name="task_start_date_ir" id="task_start_date_ir" value="<?php echo $start_date_ir ? $start_date_ir->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
		<input type="text" name="start_date_ir" id="start_date_ir" value="<?php echo $start_date_ir ? $start_date_ir->format( $df ) : "" ;?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar(document.datesFrm.start_date_ir)">
						<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0">
					</a>
	</td>
	<td width=250>&nbsp;</td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Finish Date' );?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="task_end_date" id="task_end_date" value="<?php echo $end_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : '';?>" />
		<input type="text" name="end_date" id="end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar(document.datesFrm.end_date)">
						<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0">
					</a>
	</td>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'End Date' );?> REAL </td><td>
		<input type="hidden" name="task_end_date_ir" id="task_end_date_ir" value="<?php echo $end_date_ir ? $end_date_ir->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
		<input type="text" name="end_date_ir" id="end_date_ir" value="<?php echo $end_date_ir ? $end_date_ir->format( $df ) : "" ;?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar(document.datesFrm.end_date_ir)">
						<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0">
					</a>
	</td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Expected Duration' );?>:</td>
	<td nowrap="nowrap">
		<input type="text" class="text" name="task_duration" maxlength="8" size="6" value="<?php echo isset($obj->task_duration) ? $obj->task_duration : 1;?>" />
	<select name="task_duration_type" class="text">
		<option value="24" selected="selected">d&iacute;as</option>
</select>
	</td>
	<td></td>
	<td><input type=button class="button" value="Copiar de la Base" onClick="javascript:copiarFechas();"></td>
	

</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Calculate' );?>:</td>
	<td nowrap="nowrap">
		<input type="button" value="<?php echo $AppUI->_('Duration');?>" onclick="calcDuration(document.datesFrm,0)" class="button" />
		<input type="button" value="<?php echo $AppUI->_('Finish Date');?>" onclick="calcFinish(document.datesFrm)" class="button" />
	</td>
	
</tr>






        <?php
        } else {  
        ?>
<tr>
        <td colspan='2'>
                <?php echo $AppUI->_("Only the task owner, project owner, or system administrator is able to edit time related information."); ?>
        </td>
</tr>
        <?php
        }// end of can_edit_time_information
        ?>
</table>
<input type=hidden name=task_duration_ir value=''>

<input type=hidden name="start_hour" value="08">
<input type=hidden name="start_minute" value="00">
<input type=hidden name="start_hour_ampm" value="am">	

<input type=hidden name="end_hour" value="08">
<input type=hidden name="end_minute" value="00">
<input type=hidden name="end_hour_ampm" value="am">


</form>
<script language="javascript">
 subForm.push(new FormDefinition(<?php echo $tab;?>, document.datesFrm, checkDates, saveDates));
</script>
