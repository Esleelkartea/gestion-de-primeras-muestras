<?php /* TASKS $Id: do_updatetask.php,v 1.17 2006/04/10 15:32:52 Attest sw-libre@attest.es Exp $ */
/* TASKS $Id: do_updatetask.php,v 1.16 2005/03/13 10:20:17 gregorerhardt Exp $ */

//There is an issue with international UTF characters, when stored in the database an accented letter
//actually takes up two letters per say in the field length, this is a problem with costcodes since
//they are limited in size so saving a costcode as REDACIÓN would actually save REDACIÓ since the accent takes 
//two characters, so lets unaccent them, other languages should add to the replacements array too...
function cleanText($text){
	//This text file is not utf, its iso so we have to decode/encode
	$text = utf8_decode($text);
	$trade = array('á'=>'a','à'=>'a','ã'=>'a',
                 'ä'=>'a','â'=>'a',
                 'Á'=>'A','À'=>'A','Ã'=>'A',
                 'Ä'=>'A','Â'=>'A',
                 'é'=>'e','è'=>'e',
                 'ë'=>'e','ê'=>'e',
                 'É'=>'E','È'=>'E',
                 'Ë'=>'E','Ê'=>'E',
                 'í'=>'i','ì'=>'i',
                 'ï'=>'i','î'=>'i',
                 'Í'=>'I','Ì'=>'I',
                 'Ï'=>'I','Î'=>'I',
                 'ó'=>'o','ò'=>'o','õ'=>'o',
                 'ö'=>'o','ô'=>'o',
                 'Ó'=>'O','Ò'=>'O','Õ'=>'O',
                 'Ö'=>'O','Ô'=>'O',
                 'ú'=>'u','ù'=>'u',
                 'ü'=>'u','û'=>'u',
                 'Ú'=>'U','Ù'=>'U',
                 'Ü'=>'U','Û'=>'U',
                 'Ñ'=>'N','ñ'=>'n');
    $text = strtr($text,$trade);
	$text = utf8_encode($text);

	return $text;
}

$notify_owner =  isset($_POST['task_log_notify_owner']) ? $_POST['task_log_notify_owner'] : 0;

// dylan_cuthbert: auto-transation system in-progress, leave this line commented out for now
//include( '/usr/local/translator/translate.php' );

$del = dPgetParam( $_POST, 'del', 0 );

$obj = new CTaskLog();

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

if ($obj->task_log_date) {
	$date = new CDate( $obj->task_log_date );
	$obj->task_log_date = $date->format( FMT_DATETIME_MYSQL );
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Task Log' );
if ($del) {
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT );
	}
	$AppUI->redirect();
} else {
	
	if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( @$_POST['task_log_id'] ? 'updated' : 'inserted', UI_MSG_OK, true );
	}
}

$task = new CTask();
$task->load( $obj->task_log_task );
$task->check();

$task->task_percent_complete = dPgetParam( $_POST, 'task_percent_complete', null );

if(dPgetParam($_POST, "task_end_date", "") != ""){
	$task->task_end_date = $_POST["task_end_date"];
}

if ($task->task_percent_complete >= 100 && ( ! $task->task_end_date || $task->task_end_date == '0000-00-00 00:00:00')){
	$task->task_end_date = $obj->task_log_date;
}


if ($notify_owner) {
	if ($msg = $task->notifyOwner()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	}
}


$horas = dPgetParam($_POST, "task_log_hours", "");
$sql = "select task_hours_worked from tasks where task_id=$obj->task_log_task";
$h = db_loadResult( $sql ) + $horas;
$sql = "UPDATE tasks SET task_hours_worked=$h where task_id=$obj->task_log_task";
db_exec( $sql );


 
$dia = substr($obj->task_log_date,0,10);
$dia = str_replace('-','',$dia);


$f = $task->task_end_date_ir;
$fe = str_replace("-","",$f);
$f_fin = substr($fe,0,8);

$porciento = dPgetParam( $_POST, "task_percent_complete" , "");


if (($porciento == 100)) {
	
	$task->task_duration_ir = calc_duration ( $task->task_start_date_ir , substr($obj->task_log_date,0,10).' 08:00:00' );
	$task->task_end_date_ir = substr($obj->task_log_date,0,10).' 08:00:00';		
		
}

elseif ($dia > $f_fin)  {
			
			//estamos metiendo una accion a una tarea que se supone que deberia haber terminado y aun no esta completada,
			//por lo tanto, vamos a alargar la duracion real de esta tarea en funcion del porcentaje completado.
			
			
			$duracion = $task->task_duration; //duracion presupuestada de la tarea
			$dur = calc_duration ($task->task_start_date_ir , substr($obj->task_log_date,0,10).' 08:00:00') -1;
			
			$nueva_duracion = (100 - $porciento) * $duracion / 100 ; //duracion a partir de hoy en funcion de lo completado
			
			$task->task_duration_ir = round($nueva_duracion) + $dur ;
			$task->task_end_date_ir = calc_end_date($task->task_start_date_ir,$task->task_duration_ir ,$task->task_duration_type) ;
			
}


if (($msg = $task->store())) {
	$AppUI->setMsg( $msg, UI_MSG_ERROR, true );
}
	
		

	
// Check if we need to email the task log to anyone.
$email_assignees = dPgetParam($_POST, 'email_assignees', null) ;
$email_task_contacts = dPgetParam($_POST, 'email_task_contacts', null) ;
$email_project_contacts = dPgetParam($_POST, 'email_project_contacts', null) ;
$email_others = dPgetParam($_POST, 'email_others', null) ;
$email_extras = dPgetParam($_POST, 'email_extras', null) ;
$email_dep = dPgetParam($_POST, 'email_dep_list', '');



if ($task->email_log($obj, $email_assignees, $email_task_contacts, $email_project_contacts, $email_others, $email_dep,  $email_extras)) {
	$obj->store(); // Save the updated message. It is not an error if this fails.
}
 

$AppUI->redirect("m=tasks&a=view&task_id={$obj->task_log_task}&tab=0#tasklog{$obj->task_log_id}");



function calc_end_date( $start_date=null, $durn='8', $durnType='1' ) {
	GLOBAL $AppUI;

	$cal_day_start = intval(dPgetConfig( 'cal_day_start' ));
	$cal_day_end = intval(dPgetConfig( 'cal_day_end' ));
	$daily_working_hours = intval(dPgetConfig( 'daily_working_hours' ));

	$s = new CDate( $start_date );
	$e = $s;
	$inc = $durn;
	$full_working_days = 0;
	$hours_to_add_to_last_day = 0;
	$hours_to_add_to_first_day = $durn;
	$full_working_days = ceil($durn);
	for ( $i = 1 ; $i < $full_working_days ; $i++ ) {
		$e->addDays(1);
		$e->setTime($cal_day_start, '0', '0');
		if ( !$e->isWorkingDay() )
		$full_working_days++;
	}
	$e->setHour( '08','00','00' );
	$e = prev_working_day( $e );


	return ($e->getDate(DATE_FORMAT_ISO));

} // End of calc_end_date

function prev_working_day( $dateObj ) {
	global $AppUI;
	$end = intval(dPgetConfig('cal_day_end'));
	$start = intval(dPgetConfig('cal_day_start'));
	while ( ! $dateObj->isWorkingDay() ) {
		$dateObj->addDays(-1);

	}
	$dateObj->setTime('08', '00', '00');
	return $dateObj;
}



function calc_duration( $start_date=null, $end_date=null ) { //
	GLOBAL $AppUI;
	
	$cal_day_start = intval(dPgetConfig( 'cal_day_start' ));
	$cal_day_end = intval(dPgetConfig( 'cal_day_end' ));
	
	
	$count = new CDate( $start_date );
	$end  = new CDate( $end_date );
	
	$total_dias = 0;
	
	while ( $count <= $end ) {
		if ( $count->isWorkingDay() ) {
			$total_dias++;
		}
		$count->addDays(1);
	}
	
	return ($total_dias);
}
