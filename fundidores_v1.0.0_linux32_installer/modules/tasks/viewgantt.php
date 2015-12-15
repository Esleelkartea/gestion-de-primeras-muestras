<?php /* TASKS $Id: viewgantt.php,v 1.33 2006/03/28 18:44:59 Attest sw-libre@attest.es Exp $ */
/* TASKS $Id: viewgantt.php,v 1.32 2005/04/06 23:25:46 jcgonz Exp $ */
GLOBAL $min_view, $m, $a;

// re-set the memory limit for gantt chart drawing acc. to the config value of reset_memory_limit
ini_set('memory_limit', dPgetParam($dPconfig, 'reset_memory_limit', 8*1024*1024));

$min_view = defVal( @$min_view, false);

$project_id = defVal( @$_GET['project_id'], 0);

// sdate and edate passed as unix time stamps
$sdate = dPgetParam( $_POST, 'sdate', 0 );
$edate = dPgetParam( $_POST, 'edate', 0 );
$showLabels = dPgetParam( $_POST, 'showLabels', '0' );
//if set GantChart includes user labels as captions of every GantBar
if ($showLabels!='0') {
    $showLabels='1';
}
$showWork = dPgetParam( $_POST, 'showWork', '0' );
if ($showWork!='0') {
    $showWork='1';
}

// months to scroll
$scroll_date = 1;

$display_option = dPgetParam( $_POST, 'display_option', 'this_month' );

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

if ($display_option == 'custom') {
	// custom dates
	$start_date = intval( $sdate ) ? new CDate( $sdate ) : new CDate();
	$end_date = intval( $edate ) ? new CDate( $edate ) : new CDate();
} else {
	// month
	$start_date = new CDate();
	$end_date = $start_date;
	$end_date->addMonths( $scroll_date );
}

// setup the title block
if (!@$min_view) {
	$titleBlock = new CTitleBlock( 'Gantt Chart', 'applet-48.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=tasks", "tasks list" );
	$titleBlock->addCrumb( "?m=projects&a=view&project_id=$project_id", "view this project" );
	$titleBlock->show();
}
?>
<script language="javascript">
var calendarField = '';

function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scollbars=false' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.' + calendarField );
	fld_fdate = eval( 'document.editFrm.show_' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

function scrollPrev() {
	f = document.editFrm;
<?php
	$new_start = $start_date;
	$new_end = $end_date;
	$new_start->addMonths( -$scroll_date );
	$new_end->addMonths( -$scroll_date );
	echo "f.sdate.value='".$new_start->format( FMT_TIMESTAMP_DATE )."';";
	echo "f.edate.value='".$new_end->format( FMT_TIMESTAMP_DATE )."';";
?>
	document.editFrm.display_option.value = 'custom';
	f.submit()
}

function scrollNext() {
	f = document.editFrm;
<?php
	$new_start = $start_date;
	$new_end = $end_date;
	$new_start->addMonths( $scroll_date );
	$new_end->addMonths( $scroll_date );
	echo "f.sdate.value='" . $new_start->format( FMT_TIMESTAMP_DATE ) . "';";
	echo "f.edate.value='" . $new_end->format( FMT_TIMESTAMP_DATE ) . "';";
?>
	document.editFrm.display_option.value = 'custom';
	f.submit()
}

function showThisMonth() {
	document.editFrm.display_option.value = "this_month";
	document.editFrm.submit();
}

function showFullProject() {
	document.editFrm.display_option.value = "all";
	document.editFrm.submit();
}
function download (url) {
	url = url.replace('123','\'');
	window.open(url);
	
}
</script>
<div align=center>
<form name="editFrm" method="post" action="?<?php echo "m=$m&a=$a&project_id=$project_id";?>">
<input type="hidden" name="display_option" value="<?php echo $display_option;?>" />
<table border="0" cellpadding="4" cellspacing="0">



<tr>
	<td align="left" valign="top" width="20">
<?php if ($display_option != "all") { ?>
		<a href="javascript:scrollPrev()">
			<img src="./images/prev.gif" width="16" height="16" alt="<?php echo $AppUI->_( 'previous' );?>" border="0">
		</a>
<?php } ?>
	</td>

	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'From' );?>:</td>
	<td align="left" nowrap="nowrap">
		<input type="hidden" name="sdate" value="<?php echo $start_date->format( FMT_TIMESTAMP_DATE );?>" />
		<input type="text" class="text" name="show_sdate" value="<?php echo $start_date->format( $df );?>" size="12" disabled="disabled" />
		<a href="javascript:popCalendar('sdate')"><img src="./images/calendar.gif" width="24" height="12" alt="" border="0"></a>
	</td>

	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'To' );?>:</td>
	<td align="left" nowrap="nowrap">
		<input type="hidden" name="edate" value="<?php echo $end_date->format( FMT_TIMESTAMP_DATE );?>" />
		<input type="text" class="text" name="show_edate" value="<?php echo $end_date->format( $df );?>" size="12" disabled="disabled" />
		<a href="javascript:popCalendar('edate')"><img src="./images/calendar.gif" width="24" height="12" alt="" border="0"></a>
	<td valign="top">
		<input type="checkbox" name="showLabels" <?php echo (($showLabels==1) ? "checked=true" : "");?>><?php echo 'Mostrar Asignados';?>
	</td>

	<td align="left">
		<input type="button" class="button" value="<?php echo $AppUI->_( 'submit' );?>" onclick='document.editFrm.display_option.value="custom";submit();'>
	</td>

	<td align="right" valign="top" width="20">
<?php if ($display_option != "all") { ?>
	  <a href="javascript:scrollNext()">
	  	<img src="./images/next.gif" width="16" height="16" alt="<?php echo $AppUI->_( 'next' );?>" border="0">
	  </a>
<?php } ?>
	</td>
</tr>
<input type=hidden name="ventana" value=0>
</form>
</div>
<tr>
	<td align="center" valign="bottom" colspan="7">
		<?php echo "<a href='javascript:showThisMonth()'>".$AppUI->_('show this month')."</a> : <a href='javascript:showFullProject()'>".$AppUI->_('show full project')."</a><br>"; ?>
	</td>
</tr>

</table>

<table cellspacing="0" cellpadding="0" border="0" align="center">

<tr>
	<td align="center">
<?php
if (db_loadResult( "SELECT COUNT(*) FROM tasks WHERE task_project=$project_id" )) {
	
	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$src =	  "?m=tasks&a=gantt&suppressHeaders=1&project_id=$project_id" .
	  ( $display_option == 'all' ? '' :		'&start_date=' . $start_date->format( "%Y-%m-%d" ) . '&end_date=' . $end_date->format( "%Y-%m-%d" ) ) .
	  "&width=' + ((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95) + '&showLabels=".$showLabels."&showWork=".$showWork."&ventana=0&dates=1";
	  
	echo "<script>document.write('<img src=\"$src\">')</script>\n";
	?>
	
	<form action="?m=tasks&a=printgantt&impr=1" method="POST">
		<input type=hidden name="src" value="<?=str_replace("' + ((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95) + '",'1000.00',$src)?>">
		<input type="submit" class="button" value="Imprimir">
	</form>
	
	
	<br>
	<?
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$src2 =	  "?m=tasks&a=gantt&suppressHeaders=1&project_id=$project_id" .
	  ( $display_option == 'all' ? '' :		'&start_date=' . $start_date->format( "%Y-%m-%d" ) . '&end_date=' . $end_date->format( "%Y-%m-%d" ) ) .
	  "&width=' + ((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95) + '&showLabels=".$showLabels."&showWork=".$showWork."&ventana=0&dates=2";
	  
	echo "<script>document.write('<img src=\"$src2\">')</script>\n";
	//$url = str_replace('ventana=0','ventana=1',$src);
	?>
	<form action="?m=tasks&a=printgantt&impr=1" method="POST">
		<input type=hidden name="src" value="<?=str_replace("' + ((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95) + '",'1000.00',$src2)?>">
		<input type="submit" class="button" value="Imprimir">
	</form>
	
	<br>
	<?
	
		
} else {
	echo $AppUI->_( "No tasks to display" );
}
?>
	</td>
</tr>


	
</td>
</tr>
</table>
</div>
<br />
<?php 
// reset the php memory limit to the original php.ini value
ini_restore('memory_limit');
?>