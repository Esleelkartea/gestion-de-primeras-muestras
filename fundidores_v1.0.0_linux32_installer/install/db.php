<?php // $Id: db.php,v 1.15 2006/03/27 11:00:25 Attest sw-libre@attest.es Exp $
// $Id: db.php,v 1.14 2005/04/08 01:25:07 ajdonnison Exp $
?>
<html>
<head>
	<title>Instalador</title>
	<meta name="Description" content="Instalador">
 	<link rel="stylesheet" type="text/css" href="../style/default/main.css">
</head>
<body>
<h1><img src="dp.png" align="middle" alt="dotProject Logo"/>&nbsp;Instalador</h1>
<?php
if ( $_POST['mode'] == 'upgrade')
	@include_once "../includes/config.php";
else if (is_file( "../includes/config.php" ))
	die("Security Check: dotProject seems to be already configured. Install aborted!");
else
	@include_once "../includes/config-dist.php";

?>
<form name="instFrm" action="do_install_db.php" method="post">
<input type='hidden' name='mode' value='<?php echo $_POST['mode']; ?>' />
<table cellspacing="0" cellpadding="3" border="0" class="tbl" width="100%" align="center">
        <tr>
            <td class="title" colspan="2">Configuraci&oacute;n de la Base de Datos</td>
        </tr>
         <tr>
            <td class="item">Servidor de Base de Datos <span class='warning'> <!--Nota - currently only MySQL is known to work correctly --></span></td>
            <td align="left">
		<select name="dbtype" size="1" style="width:200px;" class="text">
<?php
   if (strstr('WIN', strtoupper(PHP_OS)) !== false) {
?>
			<option value="access">MS Access</option>
			<option value="ado">Generic ADO</option>
			<option value="ado_access">ADO to MS Access Backend</option>
			<option value="ado_mssql">ADO to MS SQL Server</option>

			<option value="vfp">MS Visual FoxPro</option>
			<option value="fbsql">FrontBase</option>
<?php
}
?>
			<option value="db2">IBM DB2</option>
			<option value="ibase">Interbase 6 or earlier</option>
			<option value="firebird">Firebird</option>
			<option value="borland_ibase">Borland Interbase 6.5 and Later</option>

			<option value="informix">Informix 7.3 or later</option>
			<option value="informix72">Informix 7.2 or earlier</option>
			<option value="ldap">LDAP</option>
			<option value="mssql">MS SQL Server 7 and later</option>
			<option value="mssqlpro">Portable MS SQL Server</option>
			<option value="mysql" selected="selected">MySQL - Recomendado</option>

			<option value="mysqlt">MySQL With Transactions</option>
			<option value="maxsql">MySQL MaxDB</option>
			<option value="oci8">Oracle 8/9</option>
			<option value="oci805">Oracle 8.0.5</option>
			<option value="oci8po">Oracle 8/9 Portable</option>
			<option value="odbc">ODBC</option>

			<option value="odbc_mssql">MS SQL Server via ODBC</option>
			<option value="odbc_oracle">Oracle via ODBC</option>
			<option value="odbtp">Generic Odbtp</option>
			<option value="odbtp_unicode">Odbtp With Unicode Support</option>
			<option value="oracle">Older Oracle</option>
			<option value="netezza">Netezza</option>

			<option value="postgres">Generic PostgreSQL</option>
			<option value="postgres64">PostreSQL 6.4 and earlier</option>
			<option value="postgres7">PostgreSQL 7</option>
			<option value="sapdb">SAP DB</option>
			<option value="sqlanywhere">Sybase SQL Anywhere</option>
			<option value="sqlite">SQLite</option>

			<option value="sqlitepo">Portable SQLite</option>
			<option value="sybase">Sybase</option>
		</select>
	   </td>
  	 </tr>
         <tr>
            <td class="item">Nombre del Servidor</td>
            <td align="left"><input  type="text" name="dbhost" value="<?php echo $dPconfig['dbhost']; ?>" title="The Name of the Host the Database Server is installed on" /></td>
          </tr>
           <tr>
            <td class="item">Nombre de la Base de Datos</td>
            <td align="left"><input  type="text" name="dbname" value="<?php echo  $dPconfig['dbname']; ?>" title="The Name of the Database dotProject will use and/or install" /></td>
          </tr>
          <tr>
            <td class="item">Usuario</td>
            <td align="left"><input  type="text" name="dbuser" value="<?php echo $dPconfig['dbuser']; ?>" title="The Database User that dotProject uses for Database Connection" /></td>
          </tr>
          <tr>
            <td class="item">Contraseña</td>
            <td align="left"><input  type="text" name="dbpass" value="<?php echo $dPconfig['dbpass']; ?>" title="The Password according to the above User." /></td>
          </tr>
           <tr>
            <td class="item">Use Persistent Connection?</td>
            <td align="left"><input type="checkbox" name="dbpersist" value="1" <?php echo ($dPconfig['dbpersist']==true) ? 'checked="checked"' : ''; ?> title="Use a persistent Connection to your Database Server." /></td>
          </tr>
<?php if ($_POST['mode'] == 'install') { ?>
          <tr>
            <td class="item">Sobreescribir Base de Datos?</td>
            <td align="left"><input type="checkbox" name="dbdrop" value="1" title="Borra la base de datos antigua antes de crear la nueva. Todos los datos de la antigua base de datos se perderán y no podrán ser recuperados" /><span class="item"> Atención! Los datos de la BD actual se perderán!</span></td>
        </tr>
<?php } ?>
        </tr>
          <tr>
            <td class="title" colspan="2">&nbsp;</td>
        </tr>
           
          <tr>
            
	  <td align="center" class="item"><br />&nbsp;   		</td>
	  <td>
	  	<input class="button" type="submit" name="do_db_cfg" value="Instalar" title="Instalar" />
	  </td>
          </tr>
        </table>
</form>
</body>
</html>
