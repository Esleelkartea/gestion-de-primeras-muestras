<?php // $Id: index.php,v 1.19 2005/03/22 13:36:33 Attest sw-libre@attest.es Exp $
// $Id: index.php,v 1.18 2005/03/12 00:06:33 ajdonnison Exp $
/*
All files in this work, except the modules/ticketsmith directory, are now
covered by the following copyright notice.  The ticketsmith module is
under the Voxel Public License.  See modules/ticketsmith/LICENSE
for details.  Please note that included libraries in the lib directory
may have their own license.

Copyright (c) 2003-2005 The dotProject Development Team <core-developers@dotproject.net>

    This file is part of dotProject.

    dotProject is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    dotProject is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with dotProject; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

The full text of the GPL is in the COPYING file.
*/

$mode = 'install';
if (is_file( "../includes/config.php" )) {
	$mode = 'upgrade';
}
?>
<html>
<head>
	<title>Instalador</title>
	<meta name="Description" content="Instalador">
 	<link rel="stylesheet" type="text/css" href="../style/default/main.css">
</head>
<body>
<h1><img src="dp.png" align="middle" alt="dotProject Logo"/>&nbsp;Instalador</h1>
<!--<table cellspacing="0" cellpadding="3" border="0" class="tbl" width="90%" align="center">
<tr>
        <td class="item" colspan="2">Welcome to the dotProject Installer! It will setup the database for dotProject and create an appropriate config file.
	In some cases a manual installation cannot be avoided.
        </td>
</tr>
<tr>
        <td colspan="2">&nbsp;</td>
</tr>
<tr>
        <td class="title" colspan="2">There is an initial Check for (minimal) Requirements appended down below for troubleshooting. At least a database connection
	must be available and ../includes/config.php must be writable for the webserver!</td>
</tr>
<?php
	if ($mode == 'upgrade') {
?>
<tr>
	<td class='title' colspan='2'><p class='error'>It would appear that you already have a dotProject installation. The installar will attempt to upgrade your system, however it is a good idea to take a full backup first!</p></td>
<?php
	}
?>
<tr>
        <td colspan="2" align="center"><br /><form action="db.php" method="post" name="form" id="form">
	<input class="button" type="submit" name="next" value="Start <?php echo $mode == 'install' ? "Installation" : "Upgrade" ?>" />
	<input type="hidden" name="mode" value="<?php echo $mode; ?>" /></form></td>
</tr>
</table>-->
<table width=100%><tr><td align="center"><form action="db.php" method="post" name="form" id="form">
	<input class="button" type="submit" name="next" value="Comenzar <?php echo $mode == 'install' ? "Instalacion" : "Modificación" ?>" />
	<input type="hidden" name="mode" value="<?php echo $mode; ?>" /></form></td></tr></table>
<br />
<?php
// define some necessary variables for check inclusion
$failedImg = '<img src="../images/icons/stock_cancel-16.png" width="16" height="16" align="middle" alt="Failed"/>';
$okImg = '<img src="../images/icons/stock_ok-16.png" width="16" height="16" align="middle" alt="OK"/>';
$tblwidth = '90%';
$cfgDir = "../includes";
$cfgFile = "../includes/config.php";
$filesDir = "../files";
$locEnDir = "../locales/en";
$tmpDir = "../files/temp";
include_once("vw_idx_check.php");
?><br>

    

</body>
</html>
