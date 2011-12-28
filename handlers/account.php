<?php

/**
 * Shows a list of profiles (sites) and lets you choose the one
 * to show stats for.
 */

$this->require_admin ();

$page->title = 'Analytics - 2. Choose a profile';
$page->layout = 'admin';

$f = new Form ('post', 'analytics/account');

if ($f->submit ()) {
	$_POST['open'] = '<?php';
	$settings = parse_ini_file ('conf/analytics.php');
	$settings['open'] = '<?php';
	$settings['profile'] = $_POST['profile'];
	if (file_put_contents ('conf/analytics.php', $tpl->render ('analytics/conf', $settings))) {
		$this->add_notification (i18n_get ('Profile updated.'));
		$this->redirect ('/analytics/admin');
	} else {
		echo '<p><strong>' . i18n_get ('Failed to update settings. Please check your folder permissions and try again.') . '</strong></p>';
	}
}

if (! file_exists ('conf/analytics.php')) {
	$this->redirect ('/analytics/settings');
}

$settings = parse_ini_file ('conf/analytics.php');

// get the list of sites
$ga = new gapi ($settings['email'], $settings['pass']);
$ga->requestAccountData ();
$sites = array ();
foreach ($ga->getResults () as $result) {
	$sites[$result->getProfileId ()] = $result->getTitle ();
}

$o = new StdClass;
$o->profile = $settings['profile'];
$o = $f->merge_values ($o);
$o->sites = $sites;
$o->failed = $f->failed;

echo $tpl->render ('analytics/account', $o);

?>