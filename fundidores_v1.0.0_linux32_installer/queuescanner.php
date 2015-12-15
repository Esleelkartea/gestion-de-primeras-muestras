<?php /* $Id: queuescanner.php,v 1.4 2006/04/18 11:12:30 Attest sw-libre@attest.es Exp $ */
// $Id: queuescanner.php,v 1.3 2005/03/11 01:40:25 ajdonnison Exp $

/*
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

The full text of the GPL is in the LICENSE file.
*/

	// Function to scan the event queue and execute any functions required.
	$baseDir = dirname(__FILE__);
	require_once "$baseDir/includes/config.php";
	require_once "$baseDir/includes/main_functions.php";
	require_once "$baseDir/includes/db_connect.php";
	require_once "$baseDir/classes/ui.class.php";
	require_once "$baseDir/classes/event_queue.class.php";
	require_once "$baseDir/classes/query.class.php";

	$AppUI = new CAppUI;

	echo "Scanning Queue ...\n";
	$queue = new EventQueue;
	$queue->scan();
	echo "Done, $queue->event_count events processed\n";
?>
