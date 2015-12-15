<?php /* TASKS $Id: viewgantt.php,v 1.71 2006/04/10 17:05:38 Attest sw-libre@attest.es Exp $ */
/* TASKS $Id: viewgantt.php,v 1.7 2005/04/06 18:43:51 gregorerhardt Exp $gantt.php,v 1.30 2004/08/06 22:56:54 gregorerhardt Exp $ */
GLOBAL  $company_id, $dept_ids, $department, $min_view, $m, $a ;

ini_set('memory_limit', $dPconfig['reset_memory_limit']);

$min_view = defVal( @$min_view, false);
$project_id = defVal( @$_GET['project_id'], 0);

// sdate and edate passed as unix time stamps
$sdate = dPgetParam( $_POST, 'sdate', 0 );
$edate = dPgetParam( $_POST, 'edate', 0 );
$showInactive = dPgetParam( $_POST, 'showInactive', '0' );
$showLabels = dPgetParam( $_POST, 'showLabels', '0' );

$showAllGantt = dPgetParam( $_POST, 'showAllGantt', '0' );
$showTaskGantt = dPgetParam( $_POST, 'showTaskGantt', '0' );

//if set GantChart includes user labels as captions of every GantBar
if ($showLabels!='0') {
    $showLabels='1';
}
if ($showInactive!='0') {
    $showInactive='1';
}
$linea = $_REQUEST["linea"];
if ($showAllGantt!='0')
     $showAllGantt='1';

$projectStatus = dPgetSysVal( 'ProjectStatus' );

//ina
$project = new CProject();
//
if (isset(  $_POST['proFilter2'] )) {
	$proFilter2 = $_POST['proFilter2'];
	$pro_list= implode(',',$proFilter2);
	
} 

if (isset(  $_POST['proFilter'] )) {
	$AppUI->setState( 'ProjectIdxFilter',  $_POST['proFilter'] );
	
}
$proFilter = $AppUI->getState( 'ProjectIdxFilter' ) !== NULL ? $AppUI->getState( 'ProjectIdxFilter' ) : '-1';
if ($proFilter>-1) {
	$ps['where']='project_status='.$proFilter;
	$allowedProjects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name','', $ps );	
	
} else {
	$allowedProjects = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name');	
}

if (isset($_POST["clientes"])){
	$filtro_cliente=$_POST["clientes"];
}
else {
	$filtro_cliente='-1';
		
}



$projFilter = arrayMerge( array('-1' => 'All Status'), $projectStatus);
natsort($projFilter);


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
	$titleBlock->addCrumb( "?m=$m", "projects list" );
	$titleBlock->show();
}
?>
<script language="javascript">
//document.getElementById("buscador").style.visibility="hidden";
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
function cambioCli(idcli) {
	
	document.editFrm.submit();
}

function cambio (id,idc) {
	var ar = new Array();
	<?
	$buf='';
	$c=0;
	$ps2['where']='';
	foreach($projFilter as $pf => $va)  {
		$buf.="";
		if ($pf==-2) $ps2['where']='project_status != 3 '; //todos menos los que están en progreso
			else if ($pf==-1) {}  //todos	
				else  $ps2['where']='project_status='.$pf.' ';  //todos q estén en el estado tal
		
		if ($pf==-1) $ps2['where'].='project_client='.$filtro_cliente;
		else $ps2['where'].='AND project_client='.$filtro_cliente;
		$proy = $project->getAllowedRecords( $AppUI->user_id, 'project_id,project_name', 'project_name','', $ps2 );
		$co = count($proy);
		$c=0;
		foreach ($proy as $pr => $val) {
			if ($c==0) {
				$buf.= "ar[".$pf."]= new Array(";
			}
			$buf.= "'".$pr."#?#".$val."'";
			if ($c!=$co-1) $buf.=", ";
			else $buf.=');';
			$c++;
		}
		$buf.="\n";
	}
	echo $buf;
	//se crea un array con los proyectos que se muestran para seleccionarlos de la lista
	$n=count($proFilter2);
	$buf= "\n\n\tvar sels = new Array();\n";
	for($i=0;$i<$n;$i++){
			$buf.= "\tsels[$i]=$proFilter2[$i]\n";
	}
	echo $buf."\n";
	
	
	?>
	var numProyectos = sels.length;
	
	var ar2= new Array();
	var sel = document.getElementById('pro2');
	sel.options.length = 0;
	sel.style.width='200';
	if ((ar[id]!=null) && (ar[id]!="undefined") && (ar[id].length>0)) {
		for (var i=0 ; i<(ar[id].length) ; i++) {
			var a=new Array();
			a = ar[id][i].split('#?#');
			sel.options[i] = new Option ('- '+a[1],a[0]);
			for(var j=0;j < numProyectos;j++) 
				if(a[0]==sels[j]) sel.options[i].selected=true;
		}
	} 
	else sel.options[0] = new Option ('No hay proyectos que mostrar.','');
}

</script>

<table width="100%" cellpadding="4" cellspacing="0" class="tbl2" >
<tr>
        <td>
                <table border="0" cellpadding="4" cellspacing="0" class="tbl2">

                <form name="editFrm" method="post" action="?<?php echo "m=$m&a=$a";?>">
                <input type="hidden" name="display_option" value="<?php echo $display_option;?>" />

                <tr>
                        <td align="left" valign="top" width="20">
                <?php if ($display_option != "all") { ?>
                                <a href="javascript:scrollPrev()">
                                        <img src="./images/prev.gif" width="16" height="16" alt="<?php echo $AppUI->_( 'previous' );?>" border="0">
                                </a>
                <?php } ?>
                        </td>
						
                        
                
						
						
						
                        <td valign="top" align="right" nowrap="nowrap"><?php echo $AppUI->_( 'From' );?>:
                                <input type="hidden" name="sdate" value="<?php echo $start_date->format( FMT_TIMESTAMP_DATE );?>" />
                                <input type="text" class="text" name="show_sdate" value="<?php echo $start_date->format( $df );?>" size="12" disabled="disabled" />
                                <a href="javascript:popCalendar('sdate')"><img src="./images/calendar.gif" width="24" height="12" alt="" border="0"></a>
                        
						</br>
                        <?php echo $AppUI->_( 'To' );?>:
                                <input type="hidden" name="edate" value="<?php echo $end_date->format( FMT_TIMESTAMP_DATE );?>" />
                                <input type="text" class="text" name="show_edate" value="<?php echo $end_date->format( $df );?>" size="12" disabled="disabled" />
                                <a href="javascript:popCalendar('edate')"><img src="./images/calendar.gif" width="24" height="12" alt="" border="0"></a></td>
                        <td valign="top">
                             <?php echo $AppUI->_( 'etiq_Cliente' );?>:  
                        	
                        	<?
                        	$a_clientes=array();
                        	$q = new DBQuery();
                        	$q->addTable('companies');
                        	$q->addWhere('company_type="2"'); //Las empresas Clientes
                        	$q->addQuery('company_id, company_name');
                        	$q->addOrder('company_name');
                        	$q->exec();
                        	if($a_clientes=$q->loadList()) $ok=true; else $ok=false;
                        	$q->clear();	
													
							$buf='<select name="clientes" class="text" onChange="javascript:cambioCli(this.value)">';
								if ($ok) {
									if ($filtro_cliente==-1) $buf.='<option value=-1>Seleccione Cliente</option>';
									$buf.='<option value=0>Proyectos sin cliente</option>';
									foreach ($a_clientes as $arr)	{
										$buf.='<option value='.$arr['company_id'].' ';
										if ($filtro_cliente==$arr['company_id']) $buf.=' selected';
										$buf.='>'.$arr['company_name'].'</option>';
										
									}
								}else {
									if ($filtro_cliente==-1) $buf.='<option value=-1>Seleccione cliente</option>';
									$buf.='<option value=0>Proyectos sin cliente</option>';
									
									}
									
							$buf.='</select>';	
							echo $buf;
						?>        
                       </br></br>
                       <?php echo $AppUI->_( 'Status' );?>:  
                        	
                       <?php echo arraySelect( $projFilter, 'proFilter', 'size=1 class=text onChange="javascript:cambio(this.value);"', $proFilter, true );?>
                       
                       </td>
                         <td valign="top">
                               
                             <?   // echo arraySelect( $allowedProjects, 'proFilter2[]', 'size=6 class=text multiple ', '', false ); ?>
						<select name="proFilter2[]" id="pro2" size=6 class=text multiple  >
						<script> 
							document.getElementById('pro2').style.width = 200; 
							cambio (document.getElementById('proFilter').value,<?echo $filtro_cliente?>);
						</script>
						
						</select>
			
                                 
                        </td>
                        <td valign="top">
		                        <table width=150 border=0><tr><td>
		                                <input type="checkbox" name="showLabels" value='1' <?php echo (($showLabels==1) ? "checked=true" : "");?>><?php echo $AppUI->_( 'Show captions' );?>
		                        <br>
		                                <input type="checkbox" value='1' name="showInactive" <?php echo (($showInactive==1) ? "checked=true" : "");?>><?php echo $AppUI->_( 'Show Inactive' );?>
		                       <br>
		                                <input type="checkbox" value='1' name="showAllGantt" <?php echo (($showAllGantt==1) ? "checked=true" : "");?>><?php echo $AppUI->_( 'Show Tasks' );?>
		                        </td></tr></table>
                        </td>
                       
                         <td valign=top width=150>
                         Mostrar:<br>
                        <select name='linea' class='text'>
                        	<option value='base' <?if ($linea=='base') echo selected;?>>L&iacute;nea base</option>
                        	<option value='real' <?if ($linea=='real') echo selected;?>>L&iacute;nea real</option>
                        	<option value='ambos' <?if ($linea=='ambos') echo selected;?>>Ambas L&iacute;neas</option>
                        </select>
                        </td>                        
                        
						<td valign="top">
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
                
                
                
                

                </form>

                <tr>
                        <td align="center" valign="bottom" colspan="12">
                                <?php echo "<a href='javascript:showThisMonth()'>".$AppUI->_('show this month')."</a> : <a href='javascript:showFullProject()'>".$AppUI->_('show all')."</a><br>"; ?>
                        </td>
                </tr>

                </table>

                <table cellspacing="0" cellpadding="0" border="1" align="center" class="tbl">
                <tr>
                        <td>
                <?php
                 
                if(($filtro_cliente!=-1)&&($linea!='ambos')){
	                $src =
	                "?m=$m&a=gantt&suppressHeaders=1&prolist=$pro_list" .
	                ( $display_option == 'all' ? '' :
	                        '&start_date=' . $start_date->format( "%Y-%m-%d" ) . '&end_date=' . $end_date->format( "%Y-%m-%d" ) ) .
	                "&width=' + ((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95) + '&showLabels=$showLabels&proFilter=$proFilter&showInactive=$showInactive&company_id=$company_id&department=$department&dept_ids=$dept_ids&showAllGantt=$showAllGantt&prolist=$pro_list&clientes=$filtro_cliente&linea=$linea";
	                
	                
	                echo "<script>document.write('<img src=\"$src\">')</script>";
                }else if($linea=='ambos') {
		                $src =
		                "?m=$m&a=gantt&suppressHeaders=1&prolist=$pro_list" .
		                ( $display_option == 'all' ? '' :
		                        '&start_date=' . $start_date->format( "%Y-%m-%d" ) . '&end_date=' . $end_date->format( "%Y-%m-%d" ) ) .
		                "&width=' + ((navigator.appName=='Netscape'?window.innerWidth:document.body.offsetWidth)*0.95) + '&showLabels=$showLabels&proFilter=$proFilter&showInactive=$showInactive&company_id=$company_id&department=$department&dept_ids=$dept_ids&showAllGantt=$showAllGantt&prolist=$pro_list&clientes=$filtro_cliente&linea=base";
		                
		                
		                echo "<script>document.write('<img src=\"$src\">')</script><br>";
		                
		                $src = str_replace('linea=base','linea=real',$src);
		                echo "<script>document.write('<img src=\"$src\">')</script>";
                	
                }
                               
                
                ?>
                        </td>
                </tr>
                
                </table>
        </td>
</tr>

</table>

<?php ini_restore('memory_limit');?>