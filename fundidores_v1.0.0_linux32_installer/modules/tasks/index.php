<?php /* TASKS $Id: index.php,v 1.47 2006/04/11 17:13:45 Attest sw-libre@attest.es Exp $ */
/* TASKS $Id: index.php,v 1.46 2005/03/29 02:12:04 gregorerhardt Exp $ */
$AppUI->savePlace();
$perms =& $AppUI->acl();
// retrieve any state parameters
$user_id = $AppUI->user_id;
if($perms->checkModule("admin", "view")){ // Only sysadmins are able to change users
	if(dPgetParam($_POST, "user_id", 0) != 0){ // this means that 
		$user_id = dPgetParam($_POST, "user_id", 0);
		$AppUI->setState("user_id", $_POST["user_id"]);
	} else if ($AppUI->getState("user_id")){
		$user_id = $AppUI->getState("user_id");
	} else {
		$AppUI->setState("user_id", $user_id);
	}
}
//borrar?
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//borrar?

$user_id = $_POST["user_id"];

if (isset( $_POST["fmoldista"])) {
	$AppUI->setState( 'TaskIdxFilterMOLD', $_POST['fmoldista'] );	
}
$fmoldista = $AppUI->getState ( 'TaskIdxFilterMOLD') ?  $AppUI->getState( 'TaskIdxFilterMOLD' ) : '';


if (isset( $_POST["fcliente"])) {
	$AppUI->setState( 'TaskIdxFilterCLI', $_POST['fcliente'] );	
}
$fcliente = $AppUI->getState ( 'TaskIdxFilterCLI') ?  $AppUI->getState( 'TaskIdxFilterCLI' ) : '';

if (isset( $_POST['f'] )) {
	$AppUI->setState( 'TaskIdxFilter', $_POST['f'] );
}
$f = $AppUI->getState( 'TaskIdxFilter' ) ? $AppUI->getState( 'TaskIdxFilter' ) : 'all';

if (isset( $_POST['f2'] )) {
	$AppUI->setState( 'CompanyIdxFilter', $_POST['f2'] );
}
$f2 = $AppUI->getState( 'CompanyIdxFilter' ) ? $AppUI->getState( 'CompanyIdxFilter' ) : 'all';

if (isset( $_GET['project_id'] )) {
	$AppUI->setState( 'TaskIdxProject', $_GET['project_id'] );
}
$project_id = $AppUI->getState( 'TaskIdxProject' ) ? $AppUI->getState( 'TaskIdxProject' ) : 0;

if (isset( $_POST['departamento'] )) {
	$AppUI->setState( 'depProject', $_POST['departamento'] );
}
$dep = $AppUI->getState( 'depProject' ) ? $AppUI->getState( 'depProject' ) : 'all';

if ($_GET["impr"]!=1) { //si queremos imprimir no hay que mostrar casi nada (solamente el listado de tareas)
?>
<script>
function mostrarOcultar (id,otroid,desplazamiento) {
	
	var capa = window.document.getElementById(id).style;
	var capa2= window.document.getElementById(otroid).style;
	var pos;
	if (capa.visibility=="visible") {
		capa.visibility="hidden";
		pos = parseInt(capa2.top);
		capa2.top = desplazamiento * (-1);
		//eval(Refcapa+capa+Refestilo+Reftop+'='+desplazamiento)
	}
	else {
		capa.visibility="visible";	
		pos = parseInt(capa2.top);
		capa2.top = 0;
	}
	
}


function cambioEmpresa (id,x) {	//cargar departamentos
	
		var departamentos = new Array();
	<?
	$sql="select distinct dept_company,company_name from departments, companies where dept_company = company_id group by dept_company order by company_name";
	if ($companias = db_LoadHashList($sql)){
		$buf='';
		$j=0;
		foreach ($companias as $comp=>$name) {
			$buf .= "\ndepartamentos[$comp] = new Array(";
			$sql = " select dept_id, dept_name from departments where dept_company = $comp ";
			$departamentos = db_LoadHashList($sql);
			$i=0;
			foreach ($departamentos as $depart => $nombre) 
			{	if ($j==0) {
					$all="departamentos['all'] = new Array ('".$depart."#?#?".$nombre."'";
					$j=1;
				}else {
					$all.=",'".$depart."#?#?".$nombre."'";
				}
					
				if ($i==0) {
					$buf .= "'".$depart."#?#?".$nombre."'";
					$i=1;
				}
				else $buf .=   ",'".$depart."#?#?".$nombre."'";
			}
		$buf .= ');';
		}
		$all.=");\n";
		echo $buf;
		echo $all;
	}
	
	?>
	
	var sel = document.getElementById('departamento');
	sel.options.length = 0;
	sel.options[0] = new Option ('--Todos los departamentos--','all');
	sel.options[0].selected = true;
	
	if ((departamentos[id]!=null) && (departamentos[id]!="undefined") && (departamentos[id].length>0)) {
		for (var i=1 ; i<=(departamentos[id].length) ; i++) {
			
			var a=new Array();
			a = departamentos[id][i-1].split('#?#?');
			sel.options[i] = new Option (a[1],a[0]);
			if ((x==1)&&(a[0]=='<?php echo $dep;?>' )) sel.options[i].selected = true;  
		}
	} 
	else sel.options[0] = new Option ('No hay departamentos','all');
	
	
	//cambiarUsuarios (id,x);
	cambioDepartamento(document.getElementById('departamento').options[document.getElementById('departamento').selectedIndex].value,x)
}
	
	


function cambioDepartamento (iddep,x) { //cargar usuarios por cambio de departamento
	
	var dusuarios = new Array();
	<?
	
	$sql="  select contact_department , user_username  from users
		    left join contacts on contact_id = user_contact 
		    left join departments on dept_id = contact_department 
		    where dept_id is not null
		    group by contact_department order by contact_company";
	
	if($dusuarios = db_loadHashList($sql)) {
		$j=0;
		$buf="\n";
		foreach ($dusuarios as $did=>$uid) {
			if (strlen($did)<4) {
				$buf .= "dusuarios[$did]=new Array(";
				$sql="select user_id, concat(contact_last_name,', ',contact_first_name) as nombre from users
				left join  contacts on user_contact = contact_id
				where contact_department = $did ";
				
				$users =  db_LoadHashList($sql);
				$i=0;
				foreach ( $users as $user=>$name) {
					if ($j==0) {
						$all="dusuarios['all'] = new Array ('".$user." &/&/& ".$name."'";
						$j=1;
					}else {
						$all.=", '".$user." &/&/& ".$name."'";
					}
					
					if ($i==0) {	
						$buf.="'".$user." &/&/& ".$name."'"; 
						$i=1; 
						
					}
					else 	{
						$buf.=", '".$user." &/&/& ".$name."'";
						
					}
				}
				$buf.=");\n";	
			
		}	}
		$all.=");\n";
		echo $buf;
		echo $all;
	}
	?>
	
	var seld = document.getElementById('user_id_f');
	seld.options.length = 0;
	seld.options[0] = new Option ('--Todos los usuarios--','all');
	seld.options[0].selected = true;
	if (iddep!='all') {
		if ((dusuarios[iddep]!=null) && (dusuarios[iddep]!="undefined") && (dusuarios[iddep].length>0)) {
			
			for (var i=1 ; i<=(dusuarios[iddep].length) ; i++) {
				var a=new Array();
				a = dusuarios[iddep][i-1].split(' &/&/& ');
				seld.options[i] = new Option (a[1],a[0]);
				if ((x==1)&&(a[0]=='<?php echo $user_id;?>' )) { 
					seld.options[i].selected = true;
				} 
			}
		} else {
			seld.options[0] = new Option ('-- No hay usuarios --','all');
		}
		
	} else {
		cambiarUsuarios(document.getElementById('f2').options[document.getElementById('f2').selectedIndex].value,x) ;
	} 
}
function cambiarUsuarios(idc,x) {	//cambiar usuarios por un cambio de empresa
	
	
	var usuarios = new Array();
	<?
	//cargar el combo de usuarios q pertenecen a esta empresa
	$sql="  select contact_company , user_username  from users
		    left join contacts on contact_id = user_contact left join companies on company_id = contact_company where contact_company!=''
		    group by contact_company order by contact_company";
	
	$usuarios = db_loadHashList($sql);
	$j=0;
	$buf="\n";
	foreach ($usuarios as $cid=>$uid) {
		$buf .= "usuarios[$cid]=new Array(";
		$sql="select user_id, concat(contact_last_name,', ',contact_first_name) as nombre from users
		left join  contacts on user_contact = contact_id
		where contact_company = $cid ";
		
		$users =  db_LoadHashList($sql);
		$i=0;
		foreach ( $users as $user=>$name) {
			if ($j==0) {
					$all="usuarios['all'] = new Array ('".$user." &/&/& ".$name."'";
					$j=1;
				}else {
					$all.=", '".$user." &/&/& ".$name."'";
				}
			
			if ($i==0) {	$buf.="'".$user." &/&/& ".$name."'"; $i=1; }
			else 	$buf.=", '".$user." &/&/& ".$name."'";
		}
		$buf.=");\n";		
	}
	$all.=");\n";
	echo $all;
	echo $buf;
	?>
	
	var sel = document.getElementById('user_id_f');
	sel.options.length = 0;
	sel.options[0] = new Option ('                      ','all');
	sel.options[0].selected = true;
	
		if ((usuarios[idc]!=null) && (usuarios[idc]!="undefined") && (usuarios[idc].length>0)) {
			
			for (var i=1 ; i<=(usuarios[idc].length) ; i++) {
				var a=new Array();
				a = usuarios[idc][i-1].split(' &/&/& ');
				sel.options[i] = new Option (a[1],a[0]);
				if ((x==1)&&(a[0]=='<?php echo $user_id;?>') ) { 
					sel.options[i].selected = true; 
				} 
			}
		} 
		else sel.options[0] = new Option ('-- No hay usuarios --','all');
	 
}

</script>
<?

// get CCompany() to filter tasks by company
require_once( $AppUI->getModuleClass( 'companies' ) );

$obj = new CCompany();
//$extra['where'] = ' company_type IN (1,0) ';
//$companies = $obj->getAllowedRecords( $AppUI->user_id, 'company_id,company_name', 'company_name','', $extra );

$sql="select company_id, company_name from companies where company_type in (0,1)";
$rows = db_LoadHashList($sql);
$filters2 = arrayMerge(  array( 'all' => '--'.$AppUI->_('All Companies', UI_OUTPUT_RAW).'--' ), $rows );

// setup the title block
$titleBlock = new CTitleBlock( 'Tasks', 'applet-48.png', $m, "$m.$a" );
$titleBlock->addCell( '<input type="button" class="button" value="Imprimir resultado de la b&uacute;squeda" onClick="javascript:document.companyFilter.action=\'?m=tasks&impr=1\';document.companyFilter.submit()">', '' );

// patch 2.12.04 text to search entry box
if (isset( $_POST['searchtext2'] )) {
	$AppUI->setState( 'searchtext2', $_POST['searchtext2']);
}

$search_text2 = $AppUI->getState('searchtext2') ? $AppUI->getState('searchtext2'):'';
$search_text2 = dPformSafe($search_text2, true);
ob_start();?>


<form action="?m=tasks" method="post" name="companyFilter">

<table width=90%>

<tr>
	<td width=100%>
				<table class="std" width=100%>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align=left width=95><?echo $AppUI->_('Company') . ':';?>
						
						</td>
						<td align=left width=215>
							<? echo arraySelect( $filters2, 'f2', 'size=1 class=text onChange="javascript:cambioEmpresa(this.value,0);"', $f2, false , '',
							'', '');?>
						</td>
				
						<td align=left width=60><? echo $AppUI->_('etiq_Moldista');?>:
						
						</td>
						<td align=left width=170><?
							$sql = "select company_name, company_id from companies where company_type = 3";
							$ar_mold = db_loadList( $sql );
							$buf = '';
							$buf = '<select name = "fmoldista" class="text">' .'<option value="no"></option>' ;
							foreach ($ar_mold as $ar) {
								$buf.='<option value = '.$ar["company_id"].' ';
								if ($ar["company_id"] == $fmoldista) $buf.='selected';
								$buf.= '>'.$ar["company_name"].'</option>';
							}
							echo $buf;
							?>
						
						</td>
						<td align=left><?echo $AppUI->_('etiq_Cliente') . ':';?>
						
						</td>
						<td align=left>
						<?
							$sql = "select company_name, company_id from companies where company_type = 2";
							$ar_mold = db_loadList( $sql );
							$buf = '';
							$buf = '<select name = "fcliente" class="text">'.'<option value="no"></option>' ;
							foreach ($ar_mold as $ar) {
								$buf.='<option value = '.$ar["company_id"].' ';
								if ($ar["company_id"]==$fcliente) $buf.=' selected ';
								$buf.='>'.$ar["company_name"].'</option>';
							}
							$buf.='<input type="hidden" name = "MyC" value=0>';
							echo $buf;
							?>
						
						</td>
						<td align=center valign=center colspan=6 rowspan=3>
						<input type="submit" class="button" value="<? echo $AppUI->_('Search')?>" title="<? echo $AppUI->_('Search in name and description fields') ?>"/>
						</td>
				</tr>
				<tr><td width=10></td>
						<td align=left><?echo $AppUI->_('Department') . ':';
						?>
						
						</td>
						<td align=left>
							<select name="departamento" size=1 class=text onChange="javascript:cambioDepartamento(this.value,0);">
						</td>
				
						<td align=left> <?echo  $AppUI->_("User") . ":";?>
						
						
						</td>
						<td align=left>
							<select name='user_id' id='user_id_f' size='1' class='text' >
						</td>
						<td align=left nowrap="nowrap" width=100><?
						echo $AppUI->_("Task Filter") . ":" ;
						
						?>
						</td>
						<td align=left nowrap="nowrap">
						<?
						$filters = Array('all'=>'', 'compl'=>'Completadas','nocompl'=>'No Completadas','noas'=>'No asignadas');
						echo arraySelect( $filters, 'f', 'size=1 class=text ', $f, true , '','', '');
				
							?>
						
						</td>
				</tr>
				<tr>
					<td colspan=5 align=right>&nbsp;</td><td align=left>Nombre de tarea :</td>
					<td align=left><input type="text" class="text" SIZE="20" name="searchtext2"  value="<?echo $search_text2?>" title="<?
					echo $AppUI->_('Search in name and description fields')?>"/></td>	</tr>
				<tr><td>&nbsp;</td></tr>
				
				</table>
		</td>
	</tr>
</table>

</form>
<?
$buff=ob_get_clean();

//$titleBlock->addCell($buff,'','','');

$titleBlock->show();
//<div id="capa1" style="position:absolute;width:100;height:100;top:100;left:100;background-color:blue">

?>
&nbsp;<a href="#" onClick="mostrarOcultar('buscador','tareas',150)">mostrar/ocultar buscador</a><br>
<div id="buscador" align=center style="visibility:visible;position:static"><?
echo $buff;
?></div>

<?
$titleBlock = new CTitleBlock('');
if (dPgetParam($_GET, 'pinned') == 1)
        $titleBlock->addCrumb( '?m=tasks', 'all tasks' );
else
        $titleBlock->addCrumb( '?m=tasks&pinned=1', 'my pinned tasks' );
//$titleBlock->addCrumb( "?m=tasks&inactive=toggle", "show ".$in."active tasks" );
//$titleBlock->addCrumb( "?m=tasks&a=tasksperuser", "tasks per user" );
?><div id="tareas" style="position:relative"><?

$titleBlock->show();

}
// include the re-usable sub view
	$min_view = false;
	include("{$dPconfig['root_dir']}/modules/tasks/tasks.php");
?></div><?
function findchilddept( &$tarr, $parent, $level=1 ) {
	$level = $level+1;
	$n = count( $tarr );
	for ($x=0; $x < $n; $x++) {
		if($tarr[$x]["dept_parent"] == $parent && $tarr[$x]["dept_parent"] != $tarr[$x]["dept_id"]){
			showchilddept( $tarr[$x], $level );
			findchilddept( $tarr, $tarr[$x]["dept_id"], $level);
		}
	}
}
function showchilddept( &$a, $level=1 ) {
	Global $buffer, $department;
	$s = '<option value="'.$a["dept_id"].'"'.(isset($department)&&$department==$a["dept_id"]?'selected="selected"':'').'>';

	for ($y=0; $y < $level; $y++) {
		if ($y+1 == $level) {
			$s .= '';
		} else {
			$s .= '&nbsp;&nbsp;';
		}
	}

	$s .= '&nbsp;&nbsp;'.$a["dept_name"]."</option>\n";
	$buffer .= $s;

//	echo $s;
}

if ($_GET["impr"]!=1){
?><script>
cambioEmpresa('<?echo $f2;?>',1);
</script><?}?>
