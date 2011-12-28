<?php

/**
 * This is the settings form to set your Google Analytics site ID.
 * Saves the site ID to the file conf/analytics.php.
 */

$this->require_admin ();

$page->title = 'Analytics - 1. Settings';
$page->layout = 'admin';

$f = new Form ('post', 'analytics/admin');

if ($f->submit ()) {
	$_POST['open'] = '<?php';
	if (file_put_contents ('conf/analytics.php', $tpl->render ('analytics/conf', $_POST))) {
		$this->add_notification (i18n_get ('Settings updated.'));
		$this->redirect ('/analytics/account');
	} else {
		echo '<p><strong>' . i18n_get ('Failed to update settings. Please check your folder permissions and try again.') . '</strong></p>';
	}
}

$o = new StdClass;
if (file_exists ('conf/analytics.php')) {
	$settings = parse_ini_file ('conf/analytics.php');
	$o->site_id = $settings['site_id'];
	$o->email = $settings['email'];
	$o->pass = $settings['pass'];
	$o->profile = $settings['profile'];
}

$o = $f->merge_values ($o);
$o->failed = $f->failed;
echo $tpl->render ('analytics/settings', $o);

?>