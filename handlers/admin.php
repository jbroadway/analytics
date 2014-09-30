<?php

/**
 * This is the admin interface of the Google Analytics app.
 * It shows a summary of the stats for your site.
 */

$this->require_admin ();

$page->title = 'Analytics';
$page->layout = 'admin';

if (! @file_exists ('conf/app.analytics.' . ELEFANT_ENV . '.php')) {
	$this->redirect ('/analytics/settings');
}

$x = (object) array ('ticks' => array ());
$now = time ();
$min = $now - 2592000;
$day = $min; // 30 days ago
$k = 0;
while ($day < $now) {
	if ($k % 5 === 0) {
		// add date to x axis only every 5 days
		$x->ticks[] = array ($k, gmdate ('M j', $day));
	}
	$day += 86400;
	$k++;
}
$x->ticks[] = array (29, gmdate ('M j')); // add today to x

echo $tpl->render ('analytics/admin', array (
	'x' => $x,
	'min_year' => gmdate ('Y', $min),
	'min_month' => gmdate ('n', $min),
	'min_day' => gmdate ('j', $min)
));

?>