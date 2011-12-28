<?php

/**
 * This is the admin interface of the Google Analytics app.
 * It shows a summary of the stats for your site.
 */

$this->require_admin ();

$page->title = 'Analytics';
$page->layout = 'admin';

if (! @file_exists ('conf/analytics.php')) {
	$this->redirect ('/analytics/settings');
}

//$this->cache = 86400;

$settings = parse_ini_file ('conf/analytics.php');

$ga = new gapi ($settings['email'], $settings['pass']);

try {
	$data = array (
		(object) array ('label' => i18n_get ('Page views'), 'data' => array ()),
		(object) array ('label' => i18n_get ('Visits'), 'data' => array ()),
	);

	$x = (object) array ('ticks' => array ());

	$min = time () - 2592000; // 30 days ago

	$ga->requestReportData(
		$settings['profile'],
		array ('date'),
		array ('pageviews','visits','visitors'),
		array ('date'),
		'', // filter
		gmdate ('Y-m-d', $min),
		gmdate ('Y-m-d') // today
	);

	foreach($ga->getResults() as $k => $result) {
		if ($k % 5 === 0) {
			// add date to x axis only every 5 days
			if (preg_match ('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', $result->getDate (), $matches)) {
				$x->ticks[] = array ($k, gmdate ('M j', strtotime ($matches[1] . '-' . $matches[2] . '-' . $matches[3])));
			}
		}

		$data[0]->data[] = array ($k, $result->getPageviews ());
		$data[1]->data[] = array ($k, $result->getVisits ());
	}
	$x->ticks[] = array (29, gmdate ('M j')); // add today to x

	echo $tpl->render ('analytics/admin', array (
		'data' => $data,
		'x' => $x,
		'min_year' => gmdate ('Y', $min),
		'min_month' => gmdate ('n', $min),
		'min_day' => gmdate ('j', $min),
		'total_visitors' => $ga->getVisitors (),
		'total_visits' => $ga->getVisits (),
		'total_pageviews' => $ga->getPageviews (),
		'pages_per_visit' => round ($ga->getPageviews () / $ga->getVisits (), 2)
	));
} catch (Exception $e) {
	echo '<p><strong>Failed to retrieve results. Error info:</strong></p>';
	echo '<p>' . $e->getMessage () . '</p>';
}

?>