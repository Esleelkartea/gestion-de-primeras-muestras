<?php /* tasks_func.php, 2006/02/23 18:22:31 Attest sw-libre@attest.es Exp $*/

//ina $percent = array(0=>'0',5=>'5',10=>'10',15=>'15',20=>'20',25=>'25',30=>'30',35=>'35',40=>'40',45=>'45',50=>'50',55=>'55',60=>'60',65=>'65',70=>'70',75=>'75',80=>'80',85=>'85',90=>'90',95=>'95',100=>'100');
$percent = array(0=>'0',25=>'25',50=>'50',75=>'75',100=>'Finalizada');

// patch 2.12.04 add all finished last 7 days, my finished last 7 days
$filters = array(
	'my'           => 'My Tasks',
	'myunfinished' => 'My Unfinished Tasks',
	'allunfinished' => 'All Unfinished Tasks',
	'myproj'       => 'My Projects',
	'mycomp'       => 'All Tasks for my Company',
	'unassigned'   => 'All Tasks (unassigned)',
	'taskcreated'  => 'All Tasks I Have Created',
	'all'          => 'All Tasks');
	//'allfinished7days' => 'All Tasks Finished Last 7 Days',
	//'myfinished7days'  => 'My Tasks Finished Last 7 Days'


$status = dPgetSysVal( 'TaskStatus' );

$priority = array(
 -1 => 'low',
 0 => 'normal',
 1 => 'high'
);

?>
